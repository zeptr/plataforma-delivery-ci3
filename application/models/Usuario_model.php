<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/**
 * Usuario_model — tabela `usuarios` (autenticação comum a todos os perfis).
 */
class Usuario_model extends CI_Model
{
    protected $tabela = 'usuarios';

    public function por_id($id)
    {
        return $this->db->get_where($this->tabela, ['id' => (int) $id])->row_array();
    }

    public function por_email($email)
    {
        return $this->db->get_where($this->tabela, ['email' => $email])->row_array();
    }

    public function email_existe($email)
    {
        return $this->db->where('email', $email)->count_all_results($this->tabela) > 0;
    }

    /**
     * Cria um utilizador. A palavra-passe é guardada como hash.
     * @return int id do utilizador criado
     */
    public function criar(array $dados)
    {
        $dados['password_hash'] = hash_password($dados['password']);
        unset($dados['password']);
        $dados['criado_em'] = date('Y-m-d H:i:s');
        $this->db->insert($this->tabela, $dados);
        return (int) $this->db->insert_id();
    }

    public function actualizar($id, array $dados)
    {
        if (isset($dados['password'])) {
            $dados['password_hash'] = hash_password($dados['password']);
            unset($dados['password']);
        }
        $dados['atualizado_em'] = date('Y-m-d H:i:s');
        return $this->db->where('id', (int) $id)->update($this->tabela, $dados);
    }

    public function definir_bloqueio($id, $bloqueado)
    {
        return $this->db->where('id', (int) $id)
                        ->update($this->tabela, ['bloqueado' => $bloqueado ? 1 : 0]);
    }

    public function listar_por_perfil($perfil)
    {
        return $this->db->where('perfil', $perfil)
                        ->order_by('criado_em', 'DESC')
                        ->get($this->tabela)->result_array();
    }

    public function contar_por_perfil($perfil)
    {
        return $this->db->where('perfil', $perfil)->count_all_results($this->tabela);
    }

    // --- Recuperação de palavra-passe ---
    public function definir_token_recuperacao($email, $token)
    {
        return $this->db->where('email', $email)->update($this->tabela, [
            'token_recuperacao'        => $token,
            'token_recuperacao_expira' => date('Y-m-d H:i:s', time() + 3600),
        ]);
    }

    public function por_token($token)
    {
        return $this->db->where('token_recuperacao', $token)
                        ->where('token_recuperacao_expira >=', date('Y-m-d H:i:s'))
                        ->get($this->tabela)->row_array();
    }

    public function redefinir_password($id, $password)
    {
        return $this->db->where('id', (int) $id)->update($this->tabela, [
            'password_hash'            => hash_password($password),
            'token_recuperacao'        => null,
            'token_recuperacao_expira' => null,
            'atualizado_em'            => date('Y-m-d H:i:s'),
        ]);
    }
}
