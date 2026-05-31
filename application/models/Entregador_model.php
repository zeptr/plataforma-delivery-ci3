<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/**
 * Entregador_model — perfil de entregador, veículo, documentação e estado.
 */
class Entregador_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Usuario_model');
    }

    /** Regista utilizador (perfil entregador) + dados do entregador. */
    public function registar(array $usuario, array $entregador)
    {
        $this->db->trans_start();

        $usuario['perfil'] = 'entregador';
        $usuario_id = $this->Usuario_model->criar($usuario);
        $entregador['usuario_id'] = $usuario_id;
        $this->db->insert('entregadores', $entregador);
        $entregador_id = (int) $this->db->insert_id();

        $this->db->trans_complete();
        return $this->db->trans_status() ? $entregador_id : false;
    }

    public function por_usuario($usuario_id)
    {
        return $this->db->get_where('entregadores', ['usuario_id' => (int) $usuario_id])->row_array();
    }

    public function por_id($id)
    {
        return $this->db->select('entregadores.*, usuarios.nome, usuarios.email, usuarios.telefone, usuarios.bloqueado, usuarios.id AS usuario_id')
                        ->join('usuarios', 'usuarios.id = entregadores.usuario_id')
                        ->get_where('entregadores', ['entregadores.id' => (int) $id])->row_array();
    }

    public function listar()
    {
        return $this->db->select('entregadores.*, usuarios.nome, usuarios.email, usuarios.telefone, usuarios.bloqueado, usuarios.criado_em')
                        ->join('usuarios', 'usuarios.id = entregadores.usuario_id')
                        ->order_by('usuarios.criado_em', 'DESC')
                        ->get('entregadores')->result_array();
    }

    public function actualizar($id, array $dados)
    {
        return $this->db->where('id', (int) $id)->update('entregadores', $dados);
    }

    /** Aprovação de documentos pelo administrador. */
    public function aprovar_documentos($id, $aprovado)
    {
        return $this->db->where('id', (int) $id)
                        ->update('entregadores', ['documentos_aprovados' => $aprovado ? 1 : 0]);
    }

    public function definir_disponibilidade($id, $disponivel)
    {
        return $this->db->where('id', (int) $id)
                        ->update('entregadores', ['disponivel' => $disponivel ? 1 : 0]);
    }

    public function actualizar_localizacao($id, $lat, $lng)
    {
        return $this->db->where('id', (int) $id)
                        ->update('entregadores', ['latitude' => $lat, 'longitude' => $lng]);
    }
}
