<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/**
 * Pedido_model — núcleo operacional (MÓDULO 5).
 * Cria pedidos transaccionalmente (itens + histórico + pagamento),
 * aplica transições de estado validadas pela máquina Estado_pedido e
 * fornece as consultas usadas por todos os perfis.
 */
class Pedido_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('estado_pedido');
        $this->load->library('pagamento_proc');
    }

    /**
     * Cria um pedido completo a partir do carrinho.
     *
     * @param array $cabecalho cliente_id, loja_id, endereco_entrega, lat/lng,
     *                         metodo_pagamento, taxa_entrega, observacoes
     * @param array $itens     lista de itens do carrinho
     * @param array $pagamento resultado de Pagamento_proc::processar()
     * @return int|false id do pedido
     */
    public function criar(array $cabecalho, array $itens, array $pagamento)
    {
        if (empty($itens)) {
            return false;
        }

        $subtotal = 0.0;
        foreach ($itens as $item) {
            $subtotal += (float) $item['preco'] * (int) $item['quantidade'];
        }
        $taxa  = (float) ($cabecalho['taxa_entrega'] ?? 0);
        $total = round($subtotal + $taxa, 2);
        $agora = date('Y-m-d H:i:s');

        $this->db->trans_start();

        $this->db->insert('pedidos', [
            'cliente_id'       => (int) $cabecalho['cliente_id'],
            'loja_id'          => (int) $cabecalho['loja_id'],
            'endereco_entrega' => $cabecalho['endereco_entrega'],
            'latitude'         => $cabecalho['latitude'] ?? null,
            'longitude'        => $cabecalho['longitude'] ?? null,
            'estado'           => Estado_pedido::INICIAL,
            'subtotal'         => $subtotal,
            'taxa_entrega'     => $taxa,
            'total'            => $total,
            'metodo_pagamento' => $cabecalho['metodo_pagamento'],
            'observacoes'      => $cabecalho['observacoes'] ?? null,
            'criado_em'        => $agora,
        ]);
        $pedido_id = (int) $this->db->insert_id();

        foreach ($itens as $item) {
            $this->db->insert('pedido_itens', [
                'pedido_id'      => $pedido_id,
                'produto_id'     => $item['produto_id'],
                'nome'           => $item['nome'],
                'preco_unitario' => $item['preco'],
                'quantidade'     => $item['quantidade'],
                'subtotal'       => round((float) $item['preco'] * (int) $item['quantidade'], 2),
            ]);
        }

        $this->db->insert('pedido_historico', [
            'pedido_id'  => $pedido_id,
            'estado'     => Estado_pedido::INICIAL,
            'usuario_id' => $cabecalho['usuario_id'] ?? null,
            'criado_em'  => $agora,
        ]);

        $this->db->insert('pagamentos', [
            'pedido_id'  => $pedido_id,
            'metodo'     => $pagamento['metodo'],
            'valor'      => $total,
            'estado'     => $pagamento['estado'],
            'referencia' => $pagamento['referencia'],
            'criado_em'  => $agora,
        ]);

        $this->db->trans_complete();
        return $this->db->trans_status() ? $pedido_id : false;
    }

    public function por_id($id)
    {
        return $this->db->select('pedidos.*, lojas.nome_empresa, lojas.telefone AS loja_telefone,
                                  lojas.latitude AS loja_lat, lojas.longitude AS loja_lng,
                                  uc.nome AS cliente_nome, uc.telefone AS cliente_telefone,
                                  ue.nome AS entregador_nome, ue.telefone AS entregador_telefone')
                        ->join('lojas', 'lojas.id = pedidos.loja_id')
                        ->join('clientes', 'clientes.id = pedidos.cliente_id')
                        ->join('usuarios uc', 'uc.id = clientes.usuario_id')
                        ->join('entregadores', 'entregadores.id = pedidos.entregador_id', 'left')
                        ->join('usuarios ue', 'ue.id = entregadores.usuario_id', 'left')
                        ->get_where('pedidos', ['pedidos.id' => (int) $id])->row_array();
    }

    public function itens($pedido_id)
    {
        return $this->db->get_where('pedido_itens', ['pedido_id' => (int) $pedido_id])->result_array();
    }

    public function historico($pedido_id)
    {
        return $this->db->where('pedido_id', (int) $pedido_id)
                        ->order_by('criado_em', 'ASC')
                        ->get('pedido_historico')->result_array();
    }

    /**
     * Aplica uma transição de estado validada pela máquina de estados.
     * @return bool|string TRUE em sucesso, ou mensagem de erro
     */
    public function mudar_estado($pedido_id, $novo_estado, $usuario_id = null)
    {
        $pedido = $this->db->get_where('pedidos', ['id' => (int) $pedido_id])->row_array();
        if (!$pedido) {
            return 'Pedido inexistente.';
        }
        if (!Estado_pedido::podeTransitar($pedido['estado'], $novo_estado)) {
            return 'Transição de estado inválida: ' . Estado_pedido::rotulo($pedido['estado'])
                   . ' → ' . Estado_pedido::rotulo($novo_estado);
        }

        $agora = date('Y-m-d H:i:s');
        $this->db->trans_start();
        $this->db->where('id', (int) $pedido_id)->update('pedidos', [
            'estado'        => $novo_estado,
            'atualizado_em' => $agora,
        ]);
        $this->db->insert('pedido_historico', [
            'pedido_id'  => (int) $pedido_id,
            'estado'     => $novo_estado,
            'usuario_id' => $usuario_id,
            'criado_em'  => $agora,
        ]);
        // Pagamento em dinheiro é confirmado na entrega.
        if ($novo_estado === 'entregue') {
            $this->db->where('pedido_id', (int) $pedido_id)
                     ->where('metodo', 'dinheiro')
                     ->update('pagamentos', ['estado' => 'pago']);
        }
        $this->db->trans_complete();
        return $this->db->trans_status() ? true : 'Erro ao actualizar o pedido.';
    }

    public function atribuir_entregador($pedido_id, $entregador_id)
    {
        return $this->db->where('id', (int) $pedido_id)
                        ->update('pedidos', ['entregador_id' => (int) $entregador_id]);
    }

    // --- Listagens por perfil ---
    private function base_listagem()
    {
        return $this->db->select('pedidos.*, lojas.nome_empresa, uc.nome AS cliente_nome')
                        ->join('lojas', 'lojas.id = pedidos.loja_id')
                        ->join('clientes', 'clientes.id = pedidos.cliente_id')
                        ->join('usuarios uc', 'uc.id = clientes.usuario_id');
    }

    public function por_cliente($cliente_id)
    {
        return $this->base_listagem()->where('pedidos.cliente_id', (int) $cliente_id)
                    ->order_by('pedidos.criado_em', 'DESC')->get('pedidos')->result_array();
    }

    public function por_loja($loja_id, $estado = null)
    {
        $this->base_listagem()->where('pedidos.loja_id', (int) $loja_id);
        if ($estado) {
            $this->db->where('pedidos.estado', $estado);
        }
        return $this->db->order_by('pedidos.criado_em', 'DESC')->get('pedidos')->result_array();
    }

    public function por_entregador($entregador_id)
    {
        return $this->base_listagem()->where('pedidos.entregador_id', (int) $entregador_id)
                    ->order_by('pedidos.criado_em', 'DESC')->get('pedidos')->result_array();
    }

    /** Pedidos prontos a ser aceites por um entregador. */
    public function disponiveis_para_entrega()
    {
        return $this->base_listagem()
                    ->where('pedidos.estado', 'preparacao')
                    ->where('pedidos.entregador_id IS NULL')
                    ->order_by('pedidos.criado_em', 'ASC')->get('pedidos')->result_array();
    }

    public function todos($filtros = [])
    {
        $this->base_listagem();
        if (!empty($filtros['estado'])) {
            $this->db->where('pedidos.estado', $filtros['estado']);
        }
        return $this->db->order_by('pedidos.criado_em', 'DESC')->get('pedidos')->result_array();
    }

    public function contar()
    {
        return $this->db->count_all('pedidos');
    }
}
