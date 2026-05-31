<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/**
 * Entregador — área do entregador (MÓDULOS 2,5,6).
 * Pedidos disponíveis, aceitação, actualização de estado da entrega,
 * rota, documentos e perfil.
 */
class Entregador extends Entregador_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Pedido_model', 'Pagamento_model', 'Usuario_model']);
    }

    private function entregador_id()
    {
        return (int) $this->entregador['id'];
    }

    // ----------------------------------------------------------- Painel
    public function index()
    {
        $entregas = $this->Pedido_model->por_entregador($this->entregador_id());
        $this->render('entregador/painel', [
            'entregador' => $this->entregador,
            'em_curso' => count(array_filter($entregas, fn ($p) => in_array($p['estado'], ['entregador_aceite', 'saiu_entrega'], true))),
            'concluidas' => count(array_filter($entregas, fn ($p) => in_array($p['estado'], ['entregue', 'finalizado'], true))),
            'disponiveis' => count($this->Pedido_model->disponiveis_para_entrega()),
            'ultimos' => array_slice($entregas, 0, 6),
        ], 'Painel do entregador');
    }

    public function disponibilidade()
    {
        $this->Entregador_model->definir_disponibilidade($this->entregador_id(), !$this->entregador['disponivel']);
        redirect('entregador');
    }

    // ----------------------------------------------- Pedidos disponíveis
    public function disponiveis()
    {
        $this->render('entregador/disponiveis', [
            'pedidos' => $this->Pedido_model->disponiveis_para_entrega(),
            'aprovado' => (bool) $this->entregador['documentos_aprovados'],
        ], 'Pedidos disponíveis');
    }

    public function aceitar($id)
    {
        if (!$this->entregador['documentos_aprovados']) {
            $this->session->set_flashdata('erro', 'Os seus documentos ainda não foram aprovados pelo administrador.');
            return redirect('entregador/disponiveis');
        }
        $pedido = $this->Pedido_model->por_id($id);
        if (!$pedido || $pedido['entregador_id'] || $pedido['estado'] !== 'preparacao') {
            $this->session->set_flashdata('aviso', 'Este pedido já não está disponível.');
            return redirect('entregador/disponiveis');
        }
        $this->Pedido_model->atribuir_entregador($id, $this->entregador_id());
        $r = $this->Pedido_model->mudar_estado($id, 'entregador_aceite', $this->autenticacao->id());
        $this->session->set_flashdata($r === true ? 'sucesso' : 'erro',
            $r === true ? 'Entrega aceite!' : $r);
        redirect('entregador/entrega/' . $id);
    }

    // ------------------------------------------------ As minhas entregas
    public function entregas()
    {
        $this->render('entregador/entregas', [
            'pedidos' => $this->Pedido_model->por_entregador($this->entregador_id()),
        ], 'As minhas entregas');
    }

    public function entrega($id)
    {
        $pedido = $this->Pedido_model->por_id($id);
        if (!$pedido || (int) $pedido['entregador_id'] !== $this->entregador_id()) { show_404(); }

        $permitidos = ['saiu_entrega', 'entregue'];
        $proximos = array_values(array_intersect(Estado_pedido::proximos($pedido['estado']), $permitidos));

        $this->render('entregador/entrega', [
            'pedido' => $pedido,
            'itens' => $this->Pedido_model->itens($id),
            'historico' => $this->Pedido_model->historico($id),
            'pagamento' => $this->Pagamento_model->por_pedido($id),
            'proximos' => $proximos,
        ], 'Entrega #' . $id);
    }

    public function mudar_estado($id)
    {
        $pedido = $this->Pedido_model->por_id($id);
        if (!$pedido || (int) $pedido['entregador_id'] !== $this->entregador_id()) { show_404(); }
        $r = $this->Pedido_model->mudar_estado($id, $this->input->post('estado'), $this->autenticacao->id());
        $this->session->set_flashdata($r === true ? 'sucesso' : 'erro',
            $r === true ? 'Estado da entrega actualizado.' : $r);
        redirect('entregador/entrega/' . $id);
    }

    // -------------------------------------------------------- Documentos
    public function documentos()
    {
        if ($this->input->method() === 'post') {
            $dados = [];
            foreach (['foto_perfil', 'selfie', 'carta_frente', 'carta_verso', 'seguro'] as $campo) {
                $ficheiro = $this->_carregar_documento($campo);
                if ($ficheiro) { $dados[$campo] = $ficheiro; }
            }
            if ($dados) {
                $this->Entregador_model->actualizar($this->entregador_id(), $dados);
                $this->session->set_flashdata('sucesso', 'Documentos carregados. Aguarde aprovação do administrador.');
            }
            return redirect('entregador/documentos');
        }
        $this->render('entregador/documentos', [
            'entregador' => $this->Entregador_model->por_id($this->entregador_id()),
        ], 'Os meus documentos');
    }

    // ----------------------------------------------------------- Perfil
    public function perfil()
    {
        if ($this->input->method() === 'post') {
            $this->Usuario_model->actualizar($this->autenticacao->id(), [
                'nome' => $this->input->post('nome'),
                'telefone' => $this->input->post('telefone'),
            ]);
            $this->Entregador_model->actualizar($this->entregador_id(), [
                'tipo_veiculo' => $this->input->post('tipo_veiculo'),
                'marca' => $this->input->post('marca'),
                'modelo' => $this->input->post('modelo'),
                'cor' => $this->input->post('cor'),
                'matricula' => $this->input->post('matricula'),
                'ano_fabrico' => $this->input->post('ano_fabrico') ?: null,
            ]);
            $this->session->set_flashdata('sucesso', 'Perfil actualizado.');
            return redirect('entregador/perfil');
        }
        $this->render('entregador/perfil', [
            'entregador' => $this->Entregador_model->por_id($this->entregador_id()),
        ], 'O meu perfil');
    }

    private function _carregar_documento($campo)
    {
        if (empty($_FILES[$campo]['name'])) { return null; }
        $this->load->library('upload', [
            'upload_path' => FCPATH . 'assets/uploads/entregadores',
            'allowed_types' => 'jpg|jpeg|png|pdf',
            'max_size' => 4096,
            'encrypt_name' => true,
        ]);
        if ($this->upload->do_upload($campo)) {
            return 'entregadores/' . $this->upload->data('file_name');
        }
        return null;
    }
}
