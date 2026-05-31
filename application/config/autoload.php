<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| CARREGAMENTO AUTOMÁTICO — Plataforma de Delivery
| -------------------------------------------------------------------
*/

$autoload['packages'] = array();

$autoload['libraries'] = array('database', 'session', 'form_validation',
    'Autenticacao', 'Estado_pedido', 'Pagamento_proc', 'Carrinho_calc');

$autoload['drivers'] = array();

$autoload['helper'] = array('url', 'form', 'html', 'seguranca', 'formato');

$autoload['config'] = array();

$autoload['language'] = array();

$autoload['model'] = array();
