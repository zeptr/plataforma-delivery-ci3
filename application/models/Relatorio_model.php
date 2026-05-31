<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/**
 * Relatorio_model — estatísticas e relatórios (MÓDULO 8).
 * Suporta âmbito global (admin) ou por loja.
 */
class Relatorio_model extends CI_Model
{
    /** Resumo de indicadores. Se $loja_id for dado, restringe à loja. */
    public function resumo($loja_id = null)
    {
        $where = $loja_id ? ['loja_id' => (int) $loja_id] : [];

        $vendas = $this->db->select("COALESCE(SUM(total),0) AS total_vendas")
                           ->where($where)
                           ->where_in('estado', ['entregue', 'finalizado'])
                           ->get('pedidos')->row_array();

        $concluidas = $this->db->where($where)
                               ->where_in('estado', ['entregue', 'finalizado'])
                               ->count_all_results('pedidos');

        $canceladas = $this->db->where($where)
                               ->where('estado', 'cancelado')
                               ->count_all_results('pedidos');

        $total_pedidos = $this->db->where($where)->count_all_results('pedidos');

        return [
            'total_vendas'    => (float) $vendas['total_vendas'],
            'entregas'        => (int) $concluidas,
            'canceladas'      => (int) $canceladas,
            'total_pedidos'   => (int) $total_pedidos,
        ];
    }

    /** Vendas agregadas por mês (últimos 6 meses). */
    public function vendas_mensais($loja_id = null)
    {
        $this->db->select("DATE_FORMAT(criado_em, '%Y-%m') AS mes, COUNT(*) AS pedidos, COALESCE(SUM(total),0) AS total")
                 ->where_in('estado', ['entregue', 'finalizado']);
        if ($loja_id) {
            $this->db->where('loja_id', (int) $loja_id);
        }
        return $this->db->group_by('mes')->order_by('mes', 'DESC')->limit(6)
                        ->get('pedidos')->result_array();
    }

    /** Distribuição de pedidos por estado. */
    public function por_estado($loja_id = null)
    {
        $this->db->select('estado, COUNT(*) AS total');
        if ($loja_id) {
            $this->db->where('loja_id', (int) $loja_id);
        }
        return $this->db->group_by('estado')->get('pedidos')->result_array();
    }
}
