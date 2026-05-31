<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/**
 * Autenticacao
 * ----------------------------------------------------------------------
 * Biblioteca central de autenticação e controlo de sessão/permissões
 * (MÓDULO 1). Encapsula o início/fecho de sessão, a verificação de
 * credenciais e a protecção de rotas por perfil.
 */
class Autenticacao
{
    /** @var CI_Controller */
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->library('session');
        $this->CI->load->model('Usuario_model');
        $this->CI->load->helper('seguranca');
    }

    /**
     * Valida credenciais. Devolve o utilizador (array) ou um código de erro:
     *   'credenciais' — email/senha errados; 'bloqueado' — conta bloqueada.
     */
    public function autenticar($email, $password)
    {
        $usuario = $this->CI->Usuario_model->por_email($email);
        if (!$usuario || !verificar_password($password, $usuario['password_hash'])) {
            return 'credenciais';
        }
        if ((int) $usuario['bloqueado'] === 1) {
            return 'bloqueado';
        }
        return $usuario;
    }

    /** Regista o utilizador na sessão. */
    public function iniciar_sessao(array $usuario)
    {
        $this->CI->session->sess_regenerate(true);
        $this->CI->session->set_userdata([
            'utilizador_id' => (int) $usuario['id'],
            'nome'          => $usuario['nome'],
            'email'         => $usuario['email'],
            'perfil'        => $usuario['perfil'],
            'autenticado'   => true,
        ]);
    }

    public function terminar_sessao()
    {
        $this->CI->session->sess_destroy();
    }

    public function esta_autenticado()
    {
        return (bool) $this->CI->session->userdata('autenticado');
    }

    public function id()
    {
        return $this->CI->session->userdata('utilizador_id');
    }

    public function perfil()
    {
        return $this->CI->session->userdata('perfil');
    }

    public function nome()
    {
        return $this->CI->session->userdata('nome');
    }

    public function tem_perfil($perfil)
    {
        return $this->perfil() === $perfil;
    }

    /** URL do painel correspondente ao perfil do utilizador. */
    public function painel_do_perfil($perfil = null)
    {
        $perfil = $perfil ?: $this->perfil();
        $mapa = [
            'admin'       => 'admin',
            'cliente'     => 'cliente',
            'loja'        => 'loja',
            'entregador'  => 'entregador',
        ];
        return $mapa[$perfil] ?? 'auth';
    }

    /**
     * Protege uma rota. Se não estiver autenticado, redirecciona para o
     * login. Se o perfil não constar de $perfis_permitidos, nega acesso.
     */
    public function exigir($perfis_permitidos = [])
    {
        if (!$this->esta_autenticado()) {
            $this->CI->session->set_flashdata('erro', 'Inicie sessão para continuar.');
            redirect('login');
        }
        $perfis_permitidos = (array) $perfis_permitidos;
        if (!empty($perfis_permitidos) && !in_array($this->perfil(), $perfis_permitidos, true)) {
            show_error('Não tem permissão para aceder a esta área.', 403, 'Acesso negado');
        }
    }
}
