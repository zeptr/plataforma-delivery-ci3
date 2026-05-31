<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/**
 * Avaliacao_model — avaliações de loja e entregador feitas pelo cliente.
 */
class Avaliacao_model extends CI_Model
{
    public function criar(array $dados)
    {
        $dados['criado_em'] = date('Y-m-d H:i:s');
        $this->db->insert('avaliacoes', $dados);
        return (int) $this->db->insert_id();
    }

    public function por_pedido($pedido_id)
    {
        return $this->db->get_where('avaliacoes', ['pedido_id' => (int) $pedido_id])->row_array();
    }

    public function por_loja($loja_id)
    {
        return $this->db->where('loja_id', (int) $loja_id)
                        ->order_by('criado_em', 'DESC')
                        ->get('avaliacoes')->result_array();
    }
}
