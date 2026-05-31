<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/**
 * Produto_model — gestão de produtos e categorias de produto por loja.
 */
class Produto_model extends CI_Model
{
    protected $tabela = 'produtos';

    public function criar(array $dados)
    {
        $dados['criado_em'] = date('Y-m-d H:i:s');
        $this->db->insert($this->tabela, $dados);
        return (int) $this->db->insert_id();
    }

    public function actualizar($id, array $dados)
    {
        return $this->db->where('id', (int) $id)->update($this->tabela, $dados);
    }

    public function remover($id, $loja_id)
    {
        return $this->db->delete($this->tabela, ['id' => (int) $id, 'loja_id' => (int) $loja_id]);
    }

    public function por_id($id)
    {
        return $this->db->get_where($this->tabela, ['id' => (int) $id])->row_array();
    }

    public function por_loja($loja_id, $apenas_activos = false)
    {
        if ($apenas_activos) {
            $this->db->where('ativo', 1);
        }
        return $this->db->where('loja_id', (int) $loja_id)
                        ->order_by('nome', 'ASC')
                        ->get($this->tabela)->result_array();
    }

    /** Ajusta o stock (delta pode ser negativo). Não desce abaixo de zero. */
    public function ajustar_stock($id, $delta)
    {
        $produto = $this->por_id($id);
        if (!$produto) {
            return false;
        }
        $novo = max(0, (int) $produto['stock'] + (int) $delta);
        return $this->db->where('id', (int) $id)->update($this->tabela, ['stock' => $novo]);
    }

    public function definir_estado($id, $ativo)
    {
        return $this->db->where('id', (int) $id)->update($this->tabela, ['ativo' => $ativo ? 1 : 0]);
    }

    // --- Categorias de produto (por loja) ---
    public function categorias($loja_id)
    {
        return $this->db->where('loja_id', (int) $loja_id)
                        ->order_by('nome', 'ASC')
                        ->get('categorias_produto')->result_array();
    }

    public function criar_categoria($loja_id, $nome)
    {
        $this->db->insert('categorias_produto', ['loja_id' => (int) $loja_id, 'nome' => $nome]);
        return (int) $this->db->insert_id();
    }

    public function remover_categoria($id, $loja_id)
    {
        return $this->db->delete('categorias_produto', ['id' => (int) $id, 'loja_id' => (int) $loja_id]);
    }
}
