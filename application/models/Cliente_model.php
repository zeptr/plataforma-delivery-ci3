<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/**
 * Cliente_model — perfil de cliente, endereços e favoritos.
 */
class Cliente_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Usuario_model');
    }

    /**
     * Cria o utilizador (perfil cliente) + o registo de cliente, dentro
     * de uma transacção. Devolve o id do cliente.
     */
    public function registar(array $usuario, array $endereco = [])
    {
        $this->db->trans_start();

        $usuario['perfil'] = 'cliente';
        $usuario_id = $this->Usuario_model->criar($usuario);
        $this->db->insert('clientes', ['usuario_id' => $usuario_id]);
        $cliente_id = (int) $this->db->insert_id();

        if (!empty($endereco['endereco'])) {
            $endereco['cliente_id'] = $cliente_id;
            $endereco['principal']  = 1;
            $this->db->insert('enderecos', $endereco);
        }

        $this->db->trans_complete();
        return $this->db->trans_status() ? $cliente_id : false;
    }

    public function por_usuario($usuario_id)
    {
        return $this->db->get_where('clientes', ['usuario_id' => (int) $usuario_id])->row_array();
    }

    public function por_id($id)
    {
        return $this->db->select('clientes.*, usuarios.nome, usuarios.email, usuarios.telefone, usuarios.genero, usuarios.bloqueado')
                        ->join('usuarios', 'usuarios.id = clientes.usuario_id')
                        ->get_where('clientes', ['clientes.id' => (int) $id])->row_array();
    }

    public function listar()
    {
        return $this->db->select('clientes.id, usuarios.nome, usuarios.email, usuarios.telefone, usuarios.bloqueado, usuarios.criado_em, usuarios.id AS usuario_id')
                        ->join('usuarios', 'usuarios.id = clientes.usuario_id')
                        ->order_by('usuarios.criado_em', 'DESC')
                        ->get('clientes')->result_array();
    }

    // --- Endereços ---
    public function enderecos($cliente_id)
    {
        return $this->db->get_where('enderecos', ['cliente_id' => (int) $cliente_id])->result_array();
    }

    public function endereco($id)
    {
        return $this->db->get_where('enderecos', ['id' => (int) $id])->row_array();
    }

    public function adicionar_endereco($cliente_id, array $dados)
    {
        $dados['cliente_id'] = (int) $cliente_id;
        if (!empty($dados['principal'])) {
            $this->db->where('cliente_id', (int) $cliente_id)->update('enderecos', ['principal' => 0]);
        }
        $this->db->insert('enderecos', $dados);
        return (int) $this->db->insert_id();
    }

    public function remover_endereco($id, $cliente_id)
    {
        return $this->db->delete('enderecos', ['id' => (int) $id, 'cliente_id' => (int) $cliente_id]);
    }

    // --- Favoritos ---
    public function alternar_favorito($cliente_id, $loja_id)
    {
        $existe = $this->db->get_where('favoritos', [
            'cliente_id' => (int) $cliente_id, 'loja_id' => (int) $loja_id,
        ])->row_array();

        if ($existe) {
            $this->db->delete('favoritos', ['cliente_id' => (int) $cliente_id, 'loja_id' => (int) $loja_id]);
            return false; // deixou de ser favorito
        }
        $this->db->insert('favoritos', [
            'cliente_id' => (int) $cliente_id,
            'loja_id'    => (int) $loja_id,
            'criado_em'  => date('Y-m-d H:i:s'),
        ]);
        return true; // passou a favorito
    }

    public function favoritos($cliente_id)
    {
        return $this->db->select('lojas.*')
                        ->join('lojas', 'lojas.id = favoritos.loja_id')
                        ->where('favoritos.cliente_id', (int) $cliente_id)
                        ->get('favoritos')->result_array();
    }

    public function e_favorito($cliente_id, $loja_id)
    {
        return $this->db->get_where('favoritos', [
            'cliente_id' => (int) $cliente_id, 'loja_id' => (int) $loja_id,
        ])->num_rows() > 0;
    }
}
