<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/**
 * MY_Controller
 * ----------------------------------------------------------------------
 * Controlador base com renderização de layout (cabeçalho + conteúdo +
 * rodapé) e helpers comuns. Os controladores por perfil estendem as
 * subclasses abaixo, que aplicam o controlo de acesso (MÓDULO 1).
 */
class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url', 'form', 'seguranca', 'formato']);
        $this->load->library(['session', 'form_validation', 'autenticacao']);
    }

    /**
     * Renderiza uma view dentro do layout padrão.
     *
     * @param string $view   caminho da view (ex.: 'cliente/lojas')
     * @param array  $dados  dados para a view
     * @param string $titulo título da página
     */
    protected function render($view, $dados = [], $titulo = 'Plataforma de Delivery')
    {
        $dados['titulo_pagina'] = $titulo;
        $dados['perfil_actual'] = $this->autenticacao->perfil();
        $dados['nome_actual']   = $this->autenticacao->nome();
        $dados['conteudo']      = $this->load->view($view, $dados, true);
        $this->load->view('templates/layout', $dados);
    }

    /** Resposta JSON para pedidos AJAX. */
    protected function json($dados, $codigo = 200)
    {
        $this->output
            ->set_status_header($codigo)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($dados));
    }
}

/** Área de administração. */
class Admin_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->autenticacao->exigir('admin');
    }
}

/** Área do cliente. */
class Cliente_Controller extends MY_Controller
{
    protected $cliente_id;

    public function __construct()
    {
        parent::__construct();
        $this->autenticacao->exigir('cliente');
        $this->load->model('Cliente_model');
        $registo = $this->Cliente_model->por_usuario($this->autenticacao->id());
        $this->cliente_id = $registo ? (int) $registo['id'] : null;
    }
}

/** Área da loja/restaurante/supermercado. */
class Loja_Controller extends MY_Controller
{
    protected $loja;

    public function __construct()
    {
        parent::__construct();
        $this->autenticacao->exigir('loja');
        $this->load->model('Loja_model');
        $this->loja = $this->Loja_model->por_usuario($this->autenticacao->id());
    }
}

/** Área do entregador. */
class Entregador_Controller extends MY_Controller
{
    protected $entregador;

    public function __construct()
    {
        parent::__construct();
        $this->autenticacao->exigir('entregador');
        $this->load->model('Entregador_model');
        $this->entregador = $this->Entregador_model->por_usuario($this->autenticacao->id());
    }
}
