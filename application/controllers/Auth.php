<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/**
 * Auth — MÓDULO 1 (Autenticação)
 * Login, registo (cliente/loja/entregador), recuperação de palavra-passe
 * e fecho de sessão.
 */
class Auth extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Usuario_model', 'Cliente_model', 'Loja_model', 'Entregador_model']);
    }

    public function index()
    {
        $this->login();
    }

    // ---------------------------------------------------------------- LOGIN
    public function login()
    {
        if ($this->autenticacao->esta_autenticado()) {
            return redirect($this->autenticacao->painel_do_perfil());
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
            $this->form_validation->set_rules('password', 'Palavra-passe', 'required');

            if ($this->form_validation->run()) {
                $resultado = $this->autenticacao->autenticar(
                    $this->input->post('email'),
                    $this->input->post('password')
                );

                if ($resultado === 'credenciais') {
                    $this->session->set_flashdata('erro', 'E-mail ou palavra-passe incorrectos.');
                } elseif ($resultado === 'bloqueado') {
                    $this->session->set_flashdata('erro', 'A sua conta está bloqueada. Contacte o suporte.');
                } else {
                    $this->autenticacao->iniciar_sessao($resultado);
                    return redirect($this->autenticacao->painel_do_perfil($resultado['perfil']));
                }
            }
        }
        $this->render('auth/login', [], 'Iniciar sessão');
    }

    // --------------------------------------------------------------- REGISTO
    public function registo()
    {
        if ($this->autenticacao->esta_autenticado()) {
            return redirect($this->autenticacao->painel_do_perfil());
        }

        $perfil = $this->input->post('perfil') ?: 'cliente';

        if ($this->input->method() === 'post') {
            $this->_validar_registo($perfil);
            if ($this->form_validation->run()) {
                $criado = $this->_processar_registo($perfil);
                if ($criado) {
                    $this->session->set_flashdata('sucesso', 'Conta criada com sucesso. Já pode iniciar sessão.');
                    return redirect('login');
                }
                $this->session->set_flashdata('erro', 'Não foi possível criar a conta. Tente novamente.');
            }
        }

        $this->render('auth/registo', [
            'categorias' => $this->Loja_model->categorias(),
            'perfil_sel' => $perfil,
        ], 'Criar conta');
    }

    private function _validar_registo($perfil)
    {
        $this->form_validation->set_rules('nome', 'Nome completo', 'required|min_length[3]');
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|is_unique[usuarios.email]');
        $this->form_validation->set_rules('telefone', 'Telefone', 'required|min_length[9]');
        $this->form_validation->set_rules('password', 'Palavra-passe', 'required|min_length[6]');
        $this->form_validation->set_rules('password2', 'Confirmação', 'required|matches[password]');

        if ($perfil === 'cliente') {
            $this->form_validation->set_rules('genero', 'Género', 'required');
            $this->form_validation->set_rules('endereco', 'Endereço', 'required');
        } elseif ($perfil === 'loja') {
            $this->form_validation->set_rules('nome_empresa', 'Nome da empresa', 'required');
            $this->form_validation->set_rules('categoria_id', 'Categoria', 'required');
            $this->form_validation->set_rules('nuit', 'NUIT', 'required');
            $this->form_validation->set_rules('endereco', 'Endereço', 'required');
        } elseif ($perfil === 'entregador') {
            $this->form_validation->set_rules('bi_passaporte', 'BI/Passaporte', 'required');
            $this->form_validation->set_rules('tipo_veiculo', 'Tipo de veículo', 'required');
            $this->form_validation->set_rules('matricula', 'Matrícula', 'required');
        }
    }

    private function _processar_registo($perfil)
    {
        $usuario = [
            'nome'     => $this->input->post('nome'),
            'email'    => $this->input->post('email'),
            'telefone' => $this->input->post('telefone'),
            'password' => $this->input->post('password'),
            'genero'   => $this->input->post('genero') ?: null,
        ];

        if ($perfil === 'cliente') {
            return $this->Cliente_model->registar($usuario, [
                'endereco'  => $this->input->post('endereco'),
                'latitude'  => $this->input->post('latitude') ?: null,
                'longitude' => $this->input->post('longitude') ?: null,
            ]);
        }
        if ($perfil === 'loja') {
            return $this->Loja_model->registar($usuario, [
                'categoria_id' => (int) $this->input->post('categoria_id'),
                'nome_empresa' => $this->input->post('nome_empresa'),
                'nuit'         => $this->input->post('nuit'),
                'telefone'     => $this->input->post('telefone'),
                'email'        => $this->input->post('email'),
                'endereco'     => $this->input->post('endereco'),
                'horario'      => $this->input->post('horario'),
                'taxa_entrega' => (float) ($this->input->post('taxa_entrega') ?: 0),
                'latitude'     => $this->input->post('latitude') ?: null,
                'longitude'    => $this->input->post('longitude') ?: null,
            ]);
        }
        if ($perfil === 'entregador') {
            return $this->Entregador_model->registar($usuario, [
                'bi_passaporte'  => $this->input->post('bi_passaporte'),
                'tipo_veiculo'   => $this->input->post('tipo_veiculo'),
                'marca'          => $this->input->post('marca'),
                'modelo'         => $this->input->post('modelo'),
                'cor'            => $this->input->post('cor'),
                'matricula'      => $this->input->post('matricula'),
                'ano_fabrico'    => $this->input->post('ano_fabrico') ?: null,
                'carta_numero'   => $this->input->post('carta_numero'),
                'carta_categoria' => $this->input->post('carta_categoria'),
                'carta_validade' => $this->input->post('carta_validade') ?: null,
            ]);
        }
        return false;
    }

    // ---------------------------------------------------- RECUPERAÇÃO DE SENHA
    public function recuperar($token = null)
    {
        // Fase 2: redefinir com token válido.
        if ($token) {
            $usuario = $this->Usuario_model->por_token($token);
            if (!$usuario) {
                $this->session->set_flashdata('erro', 'Ligação de recuperação inválida ou expirada.');
                return redirect('recuperar');
            }
            if ($this->input->method() === 'post') {
                $this->form_validation->set_rules('password', 'Palavra-passe', 'required|min_length[6]');
                $this->form_validation->set_rules('password2', 'Confirmação', 'required|matches[password]');
                if ($this->form_validation->run()) {
                    $this->Usuario_model->redefinir_password($usuario['id'], $this->input->post('password'));
                    $this->session->set_flashdata('sucesso', 'Palavra-passe redefinida. Inicie sessão.');
                    return redirect('login');
                }
            }
            return $this->render('auth/redefinir', ['token' => $token], 'Redefinir palavra-passe');
        }

        // Fase 1: pedir o e-mail e gerar token.
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
            if ($this->form_validation->run()) {
                $email = $this->input->post('email');
                if ($this->Usuario_model->email_existe($email)) {
                    $tk = token_aleatorio(40);
                    $this->Usuario_model->definir_token_recuperacao($email, $tk);
                    // Em produção enviar-se-ia por e-mail; aqui mostramos a ligação.
                    $this->session->set_flashdata('info', 'Ligação de recuperação: ' . site_url('recuperar/' . $tk));
                } else {
                    $this->session->set_flashdata('info', 'Se o e-mail existir, receberá instruções de recuperação.');
                }
                return redirect('recuperar');
            }
        }
        $this->render('auth/recuperar', [], 'Recuperar palavra-passe');
    }

    // ----------------------------------------------------------------- LOGOUT
    public function logout()
    {
        $this->autenticacao->terminar_sessao();
        redirect('login');
    }
}
