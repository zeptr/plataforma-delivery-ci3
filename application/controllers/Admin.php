<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/**
 * Admin — gestão total do sistema (MÓDULOS 2, 7, 8).
 */
class Admin extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Usuario_model', 'Cliente_model', 'Loja_model',
            'Entregador_model', 'Pedido_model', 'Pagamento_model', 'Relatorio_model']);
    }

    public function index()
    {
        $this->render('admin/painel', [
            'resumo'        => $this->Relatorio_model->resumo(),
            'total_clientes' => $this->Usuario_model->contar_por_perfil('cliente'),
            'total_lojas'   => $this->Usuario_model->contar_por_perfil('loja'),
            'total_entregadores' => $this->Usuario_model->contar_por_perfil('entregador'),
            'recebido'      => $this->Pagamento_model->total_recebido(),
            'ultimos'       => array_slice($this->Pedido_model->todos(), 0, 8),
        ], 'Painel administrativo');
    }

    // ----------------------------------------------------------- Clientes
    public function clientes()
    {
        $this->render('admin/clientes', ['clientes' => $this->Cliente_model->listar()], 'Gestão de clientes');
    }

    // -------------------------------------------------------------- Lojas
    public function lojas()
    {
        $this->render('admin/lojas', ['lojas' => $this->Loja_model->listar(['incluir_inactivas' => true])], 'Gestão de lojas');
    }

    public function alternar_loja($id)
    {
        $loja = $this->Loja_model->por_id($id);
        if ($loja) {
            $this->Loja_model->definir_estado($id, !$loja['ativo']);
            $this->session->set_flashdata('sucesso', 'Estado da loja actualizado.');
        }
        redirect('admin/lojas');
    }

    // ------------------------------------------------------- Entregadores
    public function entregadores()
    {
        $this->render('admin/entregadores', ['entregadores' => $this->Entregador_model->listar()], 'Gestão de entregadores');
    }

    public function aprovar_entregador($id)
    {
        $e = $this->Entregador_model->por_id($id);
        if ($e) {
            $this->Entregador_model->aprovar_documentos($id, !$e['documentos_aprovados']);
            $this->session->set_flashdata('sucesso', 'Documentos do entregador actualizados.');
        }
        redirect('admin/entregadores');
    }

    // ------------------------------------------- Bloquear / desbloquear
    public function alternar_bloqueio($usuario_id)
    {
        $u = $this->Usuario_model->por_id($usuario_id);
        if ($u && $u['perfil'] !== 'admin') {
            $this->Usuario_model->definir_bloqueio($usuario_id, !$u['bloqueado']);
            $this->session->set_flashdata('sucesso', $u['bloqueado'] ? 'Utilizador desbloqueado.' : 'Utilizador bloqueado.');
        }
        redirect($this->input->server('HTTP_REFERER') ? $this->input->server('HTTP_REFERER') : 'admin/clientes');
    }

    // ------------------------------------------------------------ Pedidos
    public function pedidos()
    {
        $estado = $this->input->get('estado');
        $this->render('admin/pedidos', [
            'pedidos' => $this->Pedido_model->todos($estado ? ['estado' => $estado] : []),
            'estados' => Estado_pedido::todos(),
            'estado_sel' => $estado,
        ], 'Gestão de pedidos');
    }

    public function pedido($id)
    {
        $pedido = $this->Pedido_model->por_id($id);
        if (!$pedido) { show_404(); }
        $this->render('admin/pedido', [
            'pedido' => $pedido,
            'itens' => $this->Pedido_model->itens($id),
            'historico' => $this->Pedido_model->historico($id),
            'pagamento' => $this->Pagamento_model->por_pedido($id),
        ], 'Pedido #' . $id);
    }

    // --------------------------------------------------------- Pagamentos
    public function pagamentos()
    {
        $this->render('admin/pagamentos', [
            'pagamentos' => $this->Pagamento_model->listar(),
            'total' => $this->Pagamento_model->total_recebido(),
        ], 'Gestão de pagamentos');
    }

    // --------------------------------------------------------- Categorias
    public function categorias()
    {
        if ($this->input->method() === 'post') {
            $nome = trim($this->input->post('nome'));
            if ($nome) {
                $this->db->insert('categorias_loja', ['nome' => $nome, 'ativo' => 1]);
                $this->session->set_flashdata('sucesso', 'Categoria adicionada.');
            }
            return redirect('admin/categorias');
        }
        $this->render('admin/categorias', [
            'categorias' => $this->db->order_by('nome')->get('categorias_loja')->result_array(),
        ], 'Categorias');
    }

    // --------------------------------------------------------- Relatórios
    public function relatorios()
    {
        $this->render('admin/relatorios', [
            'resumo' => $this->Relatorio_model->resumo(),
            'mensais' => $this->Relatorio_model->vendas_mensais(),
            'por_estado' => $this->Relatorio_model->por_estado(),
            'recebido' => $this->Pagamento_model->total_recebido(),
        ], 'Relatórios');
    }
}
