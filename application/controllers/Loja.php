<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/**
 * Loja — área da loja/restaurante/supermercado (MÓDULOS 2,3,5,8).
 * Gestão de produtos, categorias, pedidos (com transições de estado),
 * relatórios e perfil empresarial.
 */
class Loja extends Loja_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Produto_model', 'Pedido_model', 'Pagamento_model', 'Relatorio_model']);
    }

    private function loja_id()
    {
        return (int) $this->loja['id'];
    }

    // ----------------------------------------------------------- Painel
    public function index()
    {
        $pedidos = $this->Pedido_model->por_loja($this->loja_id());
        $this->render('loja/painel', [
            'loja' => $this->loja,
            'resumo' => $this->Relatorio_model->resumo($this->loja_id()),
            'pendentes' => count(array_filter($pedidos, fn ($p) => in_array($p['estado'], ['criado', 'recebido', 'confirmado'], true))),
            'total_produtos' => count($this->Produto_model->por_loja($this->loja_id())),
            'ultimos' => array_slice($pedidos, 0, 6),
        ], 'Painel da loja');
    }

    // --------------------------------------------------------- Produtos
    public function produtos()
    {
        $this->render('loja/produtos', [
            'produtos' => $this->Produto_model->por_loja($this->loja_id()),
            'categorias' => $this->Produto_model->categorias($this->loja_id()),
        ], 'Produtos');
    }

    public function produto_form($id = null)
    {
        $produto = $id ? $this->Produto_model->por_id($id) : null;
        if ($produto && (int) $produto['loja_id'] !== $this->loja_id()) { show_404(); }
        $this->render('loja/produto_form', [
            'produto' => $produto,
            'categorias' => $this->Produto_model->categorias($this->loja_id()),
        ], $produto ? 'Editar produto' : 'Novo produto');
    }

    public function produto_guardar()
    {
        $this->form_validation->set_rules('nome', 'Nome', 'required');
        $this->form_validation->set_rules('preco', 'Preço', 'required|numeric');
        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('erro', validation_errors());
            return redirect('loja/produto_form/' . ($this->input->post('id') ?: ''));
        }

        $dados = [
            'loja_id' => $this->loja_id(),
            'categoria_produto_id' => $this->input->post('categoria_produto_id') ?: null,
            'nome' => $this->input->post('nome'),
            'descricao' => $this->input->post('descricao'),
            'preco' => (float) $this->input->post('preco'),
            'preco_promocional' => $this->input->post('preco_promocional') ?: null,
            'stock' => (int) $this->input->post('stock'),
            'ativo' => $this->input->post('ativo') ? 1 : 0,
        ];

        $imagem = $this->_carregar_imagem('imagem', 'produtos');
        if ($imagem) { $dados['imagem'] = $imagem; }

        $id = $this->input->post('id');
        if ($id) {
            $this->Produto_model->actualizar($id, $dados);
            $this->session->set_flashdata('sucesso', 'Produto actualizado.');
        } else {
            $this->Produto_model->criar($dados);
            $this->session->set_flashdata('sucesso', 'Produto criado.');
        }
        redirect('loja/produtos');
    }

    public function produto_remover($id)
    {
        $this->Produto_model->remover($id, $this->loja_id());
        $this->session->set_flashdata('sucesso', 'Produto removido.');
        redirect('loja/produtos');
    }

    // ------------------------------------------------------- Categorias
    public function categorias()
    {
        if ($this->input->method() === 'post') {
            $nome = trim($this->input->post('nome'));
            if ($nome) {
                $this->Produto_model->criar_categoria($this->loja_id(), $nome);
                $this->session->set_flashdata('sucesso', 'Categoria criada.');
            }
            return redirect('loja/categorias');
        }
        $this->render('loja/categorias', [
            'categorias' => $this->Produto_model->categorias($this->loja_id()),
        ], 'Categorias de produto');
    }

    public function categoria_remover($id)
    {
        $this->Produto_model->remover_categoria($id, $this->loja_id());
        redirect('loja/categorias');
    }

    // ---------------------------------------------------------- Pedidos
    public function pedidos()
    {
        $estado = $this->input->get('estado');
        $this->render('loja/pedidos', [
            'pedidos' => $this->Pedido_model->por_loja($this->loja_id(), $estado),
            'estados' => Estado_pedido::todos(),
            'estado_sel' => $estado,
        ], 'Pedidos');
    }

    public function pedido($id)
    {
        $pedido = $this->Pedido_model->por_id($id);
        if (!$pedido || (int) $pedido['loja_id'] !== $this->loja_id()) { show_404(); }

        // Estados que a loja pode aplicar.
        $permitidos_loja = ['recebido', 'confirmado', 'preparacao', 'finalizado', 'cancelado'];
        $proximos = array_values(array_intersect(Estado_pedido::proximos($pedido['estado']), $permitidos_loja));

        $this->render('loja/pedido', [
            'pedido' => $pedido,
            'itens' => $this->Pedido_model->itens($id),
            'historico' => $this->Pedido_model->historico($id),
            'pagamento' => $this->Pagamento_model->por_pedido($id),
            'proximos' => $proximos,
        ], 'Pedido #' . $id);
    }

    public function mudar_estado($id)
    {
        $pedido = $this->Pedido_model->por_id($id);
        if (!$pedido || (int) $pedido['loja_id'] !== $this->loja_id()) { show_404(); }
        $r = $this->Pedido_model->mudar_estado($id, $this->input->post('estado'), $this->autenticacao->id());
        $this->session->set_flashdata($r === true ? 'sucesso' : 'erro',
            $r === true ? 'Estado do pedido actualizado.' : $r);
        redirect('loja/pedido/' . $id);
    }

    // --------------------------------------------------------- Relatórios
    public function relatorios()
    {
        $this->render('loja/relatorios', [
            'resumo' => $this->Relatorio_model->resumo($this->loja_id()),
            'mensais' => $this->Relatorio_model->vendas_mensais($this->loja_id()),
            'por_estado' => $this->Relatorio_model->por_estado($this->loja_id()),
        ], 'Relatórios de vendas');
    }

    // ----------------------------------------------------------- Perfil
    public function perfil()
    {
        if ($this->input->method() === 'post') {
            $dados = [
                'nome_empresa' => $this->input->post('nome_empresa'),
                'telefone' => $this->input->post('telefone'),
                'endereco' => $this->input->post('endereco'),
                'horario' => $this->input->post('horario'),
                'taxa_entrega' => (float) $this->input->post('taxa_entrega'),
                'latitude' => $this->input->post('latitude') ?: null,
                'longitude' => $this->input->post('longitude') ?: null,
            ];
            $logo = $this->_carregar_imagem('logotipo', 'lojas');
            if ($logo) { $dados['logotipo'] = $logo; }
            $this->Loja_model->actualizar($this->loja_id(), $dados);
            $this->session->set_flashdata('sucesso', 'Perfil da loja actualizado.');
            return redirect('loja/perfil');
        }
        $this->render('loja/perfil', [
            'loja' => $this->Loja_model->por_id($this->loja_id()),
            'categorias' => $this->Loja_model->categorias(),
        ], 'Perfil da loja');
    }

    // ------------------------------------------------ Upload de imagem
    private function _carregar_imagem($campo, $pasta)
    {
        if (empty($_FILES[$campo]['name'])) { return null; }
        $this->load->library('upload', [
            'upload_path' => FCPATH . 'assets/uploads/' . $pasta,
            'allowed_types' => 'jpg|jpeg|png|gif|webp',
            'max_size' => 4096,
            'encrypt_name' => true,
        ]);
        if ($this->upload->do_upload($campo)) {
            return $pasta . '/' . $this->upload->data('file_name');
        }
        $this->session->set_flashdata('aviso', 'Imagem não carregada: ' . strip_tags($this->upload->display_errors()));
        return null;
    }
}
