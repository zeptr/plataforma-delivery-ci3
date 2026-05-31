<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/**
 * Loja_model — lojas/restaurantes/supermercados e respectivas categorias.
 */
class Loja_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Usuario_model');
    }

    /** Regista utilizador (perfil loja) + dados da loja numa transacção. */
    public function registar(array $usuario, array $loja)
    {
        $this->db->trans_start();

        $usuario['perfil'] = 'loja';
        $usuario_id = $this->Usuario_model->criar($usuario);
        $loja['usuario_id'] = $usuario_id;
        $this->db->insert('lojas', $loja);
        $loja_id = (int) $this->db->insert_id();

        $this->db->trans_complete();
        return $this->db->trans_status() ? $loja_id : false;
    }

    public function por_usuario($usuario_id)
    {
        return $this->db->get_where('lojas', ['usuario_id' => (int) $usuario_id])->row_array();
    }

    public function por_id($id)
    {
        return $this->db->select('lojas.*, categorias_loja.nome AS categoria_nome, usuarios.bloqueado, usuarios.id AS usuario_id')
                        ->join('categorias_loja', 'categorias_loja.id = lojas.categoria_id', 'left')
                        ->join('usuarios', 'usuarios.id = lojas.usuario_id')
                        ->get_where('lojas', ['lojas.id' => (int) $id])->row_array();
    }

    public function actualizar($id, array $dados)
    {
        return $this->db->where('id', (int) $id)->update('lojas', $dados);
    }

    /**
     * Lista lojas activas, com pesquisa por nome e filtro por categoria.
     */
    public function listar($filtros = [])
    {
        $this->db->select('lojas.*, categorias_loja.nome AS categoria_nome')
                 ->join('categorias_loja', 'categorias_loja.id = lojas.categoria_id', 'left');

        if (empty($filtros['incluir_inactivas'])) {
            $this->db->where('lojas.ativo', 1);
        }
        if (!empty($filtros['pesquisa'])) {
            $this->db->like('lojas.nome_empresa', $filtros['pesquisa']);
        }
        if (!empty($filtros['categoria_id'])) {
            $this->db->where('lojas.categoria_id', (int) $filtros['categoria_id']);
        }
        return $this->db->order_by('lojas.nome_empresa', 'ASC')->get('lojas')->result_array();
    }

    public function definir_estado($id, $ativo)
    {
        return $this->db->where('id', (int) $id)->update('lojas', ['ativo' => $ativo ? 1 : 0]);
    }

    public function contar()
    {
        return $this->db->count_all('lojas');
    }

    // --- Categorias de loja (globais) ---
    public function categorias()
    {
        return $this->db->where('ativo', 1)->order_by('nome', 'ASC')
                        ->get('categorias_loja')->result_array();
    }

    public function media_avaliacoes($loja_id)
    {
        $r = $this->db->select('AVG(nota_loja) AS media, COUNT(*) AS total')
                      ->where('loja_id', (int) $loja_id)
                      ->where('nota_loja IS NOT NULL')
                      ->get('avaliacoes')->row_array();
        return ['media' => round((float) ($r['media'] ?? 0), 1), 'total' => (int) ($r['total'] ?? 0)];
    }
}
