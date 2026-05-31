<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/**
 * Dashboard — encaminha o utilizador autenticado para o painel do seu
 * perfil (MÓDULO 2).
 */
class Dashboard extends MY_Controller
{
    public function index()
    {
        $this->autenticacao->exigir();
        redirect($this->autenticacao->painel_do_perfil());
    }
}
