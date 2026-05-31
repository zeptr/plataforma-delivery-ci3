<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/**
 * Pagamento_model — consulta e gestão de pagamentos (MÓDULO 7).
 */
class Pagamento_model extends CI_Model
{
    public function por_pedido($pedido_id)
    {
        return $this->db->get_where('pagamentos', ['pedido_id' => (int) $pedido_id])->row_array();
    }

    public function listar($filtros = [])
    {
        $this->db->select('pagamentos.*, pedidos.cliente_id, lojas.nome_empresa')
                 ->join('pedidos', 'pedidos.id = pagamentos.pedido_id')
                 ->join('lojas', 'lojas.id = pedidos.loja_id');
        if (!empty($filtros['estado'])) {
            $this->db->where('pagamentos.estado', $filtros['estado']);
        }
        if (!empty($filtros['metodo'])) {
            $this->db->where('pagamentos.metodo', $filtros['metodo']);
        }
        return $this->db->order_by('pagamentos.criado_em', 'DESC')
                        ->get('pagamentos')->result_array();
    }

    public function definir_estado($id, $estado)
    {
        return $this->db->where('id', (int) $id)->update('pagamentos', ['estado' => $estado]);
    }

    public function total_recebido()
    {
        $r = $this->db->select('SUM(valor) AS total')
                      ->where('estado', 'pago')
                      ->get('pagamentos')->row_array();
        return (float) ($r['total'] ?? 0);
    }
}
