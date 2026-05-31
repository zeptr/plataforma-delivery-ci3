<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/**
 * Cliente — área do cliente (MÓDULOS 2,3,4,5,6,7).
 * Pesquisa de lojas, carrinho, pedidos, acompanhamento e avaliações.
 */
class Cliente extends Cliente_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Loja_model', 'Produto_model', 'Pedido_model',
            'Cliente_model', 'Pagamento_model', 'Avaliacao_model', 'Usuario_model']);
    }

    // ----------------------------------------------------------- Painel
    public function index()
    {
        $pedidos = $this->Pedido_model->por_cliente($this->cliente_id);
        $this->render('cliente/painel', [
            'total_pedidos' => count($pedidos),
            'em_curso' => count(array_filter($pedidos, fn ($p) => !in_array($p['estado'], ['finalizado', 'cancelado'], true))),
            'favoritos' => count($this->Cliente_model->favoritos($this->cliente_id)),
            'ultimos' => array_slice($pedidos, 0, 5),
        ], 'O meu painel');
    }

    // ------------------------------------------------------------ Lojas
    public function lojas()
    {
        $this->render('cliente/lojas', [
            'lojas' => $this->Loja_model->listar([
                'pesquisa' => $this->input->get('pesquisa'),
                'categoria_id' => $this->input->get('categoria_id'),
            ]),
            'categorias' => $this->Loja_model->categorias(),
            'pesquisa' => $this->input->get('pesquisa'),
            'categoria_sel' => $this->input->get('categoria_id'),
        ], 'Lojas');
    }

    public function loja($id)
    {
        $loja = $this->Loja_model->por_id($id);
        if (!$loja || !$loja['ativo']) { show_404(); }
        $this->render('cliente/loja', [
            'loja' => $loja,
            'produtos' => $this->Produto_model->por_loja($id, true),
            'categorias' => $this->Produto_model->categorias($id),
            'avaliacoes' => $this->Loja_model->media_avaliacoes($id),
            'e_favorito' => $this->Cliente_model->e_favorito($this->cliente_id, $id),
        ], $loja['nome_empresa']);
    }

    // --------------------------------------------------------- Carrinho
    private function carrinho()
    {
        return $this->session->userdata('carrinho') ?: ['loja_id' => null, 'itens' => []];
    }

    public function carrinho_adicionar()
    {
        $produto = $this->Produto_model->por_id($this->input->post('produto_id'));
        if (!$produto || !$produto['ativo']) {
            return $this->json(['ok' => false, 'msg' => 'Produto indisponível.'], 404);
        }
        $carrinho = $this->carrinho();

        // Um carrinho só pode conter produtos de uma loja.
        if ($carrinho['loja_id'] && (int) $carrinho['loja_id'] !== (int) $produto['loja_id']) {
            $carrinho = ['loja_id' => null, 'itens' => []];
        }
        $carrinho['loja_id'] = (int) $produto['loja_id'];

        $preco = $produto['preco_promocional'] ?: $produto['preco'];
        $carrinho['itens'] = Carrinho_calc::adicionar($carrinho['itens'],
            ['produto_id' => $produto['id'], 'nome' => $produto['nome'], 'preco' => $preco],
            (int) $this->input->post('quantidade') ?: 1);

        $this->session->set_userdata('carrinho', $carrinho);
        $this->json([
            'ok' => true, 'msg' => 'Produto adicionado ao carrinho.',
            'artigos' => Carrinho_calc::totalArtigos($carrinho['itens']),
            '_csrf' => $this->security->get_csrf_hash(),
        ]);
    }

    public function ver_carrinho()
    {
        $carrinho = $this->carrinho();
        $loja = $carrinho['loja_id'] ? $this->Loja_model->por_id($carrinho['loja_id']) : null;
        $taxa = $loja ? (float) $loja['taxa_entrega'] : 0;
        $this->render('cliente/carrinho', [
            'itens' => $carrinho['itens'],
            'loja' => $loja,
            'subtotal' => Carrinho_calc::subtotal($carrinho['itens']),
            'taxa' => $taxa,
            'total' => Carrinho_calc::total($carrinho['itens'], $taxa),
        ], 'Carrinho');
    }

    public function carrinho_actualizar()
    {
        $carrinho = $this->carrinho();
        $carrinho['itens'] = Carrinho_calc::actualizar($carrinho['itens'],
            $this->input->post('produto_id'), $this->input->post('quantidade'));
        if (empty($carrinho['itens'])) { $carrinho['loja_id'] = null; }
        $this->session->set_userdata('carrinho', $carrinho);
        redirect('cliente/ver_carrinho');
    }

    public function carrinho_remover($produto_id)
    {
        $carrinho = $this->carrinho();
        $carrinho['itens'] = Carrinho_calc::remover($carrinho['itens'], $produto_id);
        if (empty($carrinho['itens'])) { $carrinho['loja_id'] = null; }
        $this->session->set_userdata('carrinho', $carrinho);
        redirect('cliente/ver_carrinho');
    }

    // --------------------------------------------------------- Checkout
    public function checkout()
    {
        $carrinho = $this->carrinho();
        if (empty($carrinho['itens'])) {
            $this->session->set_flashdata('aviso', 'O seu carrinho está vazio.');
            return redirect('cliente/lojas');
        }
        $loja = $this->Loja_model->por_id($carrinho['loja_id']);

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('endereco_id', 'Endereço', 'required');
            $this->form_validation->set_rules('metodo_pagamento', 'Método de pagamento', 'required');
            if ($this->form_validation->run()) {
                return $this->_finalizar_pedido($carrinho, $loja);
            }
        }

        $this->render('cliente/checkout', [
            'loja' => $loja,
            'itens' => $carrinho['itens'],
            'subtotal' => Carrinho_calc::subtotal($carrinho['itens']),
            'taxa' => (float) $loja['taxa_entrega'],
            'total' => Carrinho_calc::total($carrinho['itens'], (float) $loja['taxa_entrega']),
            'enderecos' => $this->Cliente_model->enderecos($this->cliente_id),
        ], 'Finalizar pedido');
    }

    private function _finalizar_pedido($carrinho, $loja)
    {
        $endereco = $this->Cliente_model->endereco($this->input->post('endereco_id'));
        $metodo = $this->input->post('metodo_pagamento');
        $taxa = (float) $loja['taxa_entrega'];
        $total = Carrinho_calc::total($carrinho['itens'], $taxa);

        try {
            $pagamento = Pagamento_proc::processar($metodo, $total, $this->input->post('numero_movel'));
        } catch (InvalidArgumentException $e) {
            $this->session->set_flashdata('erro', $e->getMessage());
            return redirect('cliente/checkout');
        }

        $pedido_id = $this->Pedido_model->criar([
            'cliente_id' => $this->cliente_id,
            'loja_id' => $carrinho['loja_id'],
            'usuario_id' => $this->autenticacao->id(),
            'endereco_entrega' => $endereco['endereco'],
            'latitude' => $endereco['latitude'],
            'longitude' => $endereco['longitude'],
            'metodo_pagamento' => $metodo,
            'taxa_entrega' => $taxa,
            'observacoes' => $this->input->post('observacoes'),
        ], array_values($carrinho['itens']), $pagamento);

        if ($pedido_id) {
            // Reduz o stock dos produtos encomendados.
            foreach ($carrinho['itens'] as $item) {
                $this->Produto_model->ajustar_stock($item['produto_id'], -$item['quantidade']);
            }
            $this->session->unset_userdata('carrinho');
            $this->session->set_flashdata('sucesso', 'Pedido #' . $pedido_id . ' criado com sucesso!');
            return redirect('cliente/acompanhar/' . $pedido_id);
        }
        $this->session->set_flashdata('erro', 'Não foi possível criar o pedido.');
        redirect('cliente/checkout');
    }

    // ---------------------------------------------------------- Pedidos
    public function pedidos()
    {
        $this->render('cliente/pedidos', [
            'pedidos' => $this->Pedido_model->por_cliente($this->cliente_id),
        ], 'Os meus pedidos');
    }

    public function acompanhar($id)
    {
        $pedido = $this->Pedido_model->por_id($id);
        if (!$pedido || (int) $pedido['cliente_id'] !== $this->cliente_id) { show_404(); }
        $this->render('cliente/acompanhar', [
            'pedido' => $pedido,
            'itens' => $this->Pedido_model->itens($id),
            'historico' => $this->Pedido_model->historico($id),
            'pagamento' => $this->Pagamento_model->por_pedido($id),
            'avaliacao' => $this->Avaliacao_model->por_pedido($id),
        ], 'Pedido #' . $id);
    }

    public function cancelar($id)
    {
        $pedido = $this->Pedido_model->por_id($id);
        if ($pedido && (int) $pedido['cliente_id'] === $this->cliente_id) {
            $r = $this->Pedido_model->mudar_estado($id, 'cancelado', $this->autenticacao->id());
            $this->session->set_flashdata($r === true ? 'sucesso' : 'erro',
                $r === true ? 'Pedido cancelado.' : $r);
        }
        redirect('cliente/acompanhar/' . $id);
    }

    public function avaliar($id)
    {
        $pedido = $this->Pedido_model->por_id($id);
        if ($pedido && (int) $pedido['cliente_id'] === $this->cliente_id && in_array($pedido['estado'], ['entregue', 'finalizado'], true)) {
            if (!$this->Avaliacao_model->por_pedido($id)) {
                $this->Avaliacao_model->criar([
                    'pedido_id' => $id,
                    'cliente_id' => $this->cliente_id,
                    'loja_id' => $pedido['loja_id'],
                    'entregador_id' => $pedido['entregador_id'],
                    'nota_loja' => (int) $this->input->post('nota_loja'),
                    'nota_entregador' => $pedido['entregador_id'] ? (int) $this->input->post('nota_entregador') : null,
                    'comentario' => $this->input->post('comentario'),
                ]);
                $this->session->set_flashdata('sucesso', 'Obrigado pela sua avaliação!');
            }
        }
        redirect('cliente/acompanhar/' . $id);
    }

    // -------------------------------------------------------- Favoritos
    public function favoritos()
    {
        $this->render('cliente/favoritos', [
            'lojas' => $this->Cliente_model->favoritos($this->cliente_id),
        ], 'Favoritos');
    }

    public function favorito_alternar($loja_id)
    {
        $agora = $this->Cliente_model->alternar_favorito($this->cliente_id, $loja_id);
        if ($this->input->is_ajax_request()) {
            return $this->json(['ok' => true, 'favorito' => $agora, '_csrf' => $this->security->get_csrf_hash()]);
        }
        redirect($this->input->server('HTTP_REFERER') ?: 'cliente/lojas');
    }

    // -------------------------------------------------------- Endereços
    public function enderecos()
    {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('endereco', 'Endereço', 'required');
            if ($this->form_validation->run()) {
                $this->Cliente_model->adicionar_endereco($this->cliente_id, [
                    'etiqueta' => $this->input->post('etiqueta') ?: 'Casa',
                    'endereco' => $this->input->post('endereco'),
                    'latitude' => $this->input->post('latitude') ?: null,
                    'longitude' => $this->input->post('longitude') ?: null,
                    'principal' => $this->input->post('principal') ? 1 : 0,
                ]);
                $this->session->set_flashdata('sucesso', 'Endereço adicionado.');
            }
            return redirect('cliente/enderecos');
        }
        $this->render('cliente/enderecos', [
            'enderecos' => $this->Cliente_model->enderecos($this->cliente_id),
        ], 'Os meus endereços');
    }

    public function endereco_remover($id)
    {
        $this->Cliente_model->remover_endereco($id, $this->cliente_id);
        $this->session->set_flashdata('sucesso', 'Endereço removido.');
        redirect('cliente/enderecos');
    }

    // ----------------------------------------------------------- Perfil
    public function perfil()
    {
        if ($this->input->method() === 'post') {
            $dados = [
                'nome' => $this->input->post('nome'),
                'telefone' => $this->input->post('telefone'),
                'genero' => $this->input->post('genero'),
            ];
            if ($this->input->post('password')) { $dados['password'] = $this->input->post('password'); }
            $this->Usuario_model->actualizar($this->autenticacao->id(), $dados);
            $this->session->set_flashdata('sucesso', 'Perfil actualizado.');
            return redirect('cliente/perfil');
        }
        $this->render('cliente/perfil', [
            'usuario' => $this->Usuario_model->por_id($this->autenticacao->id()),
        ], 'O meu perfil');
    }
}
