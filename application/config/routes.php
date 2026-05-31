<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| ROTAS — Plataforma de Delivery
| -------------------------------------------------------------------
*/

$route['default_controller'] = 'auth';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// --- Autenticação ---
$route['login']      = 'auth/login';
$route['logout']     = 'auth/logout';
$route['registo']    = 'auth/registo';
$route['recuperar']  = 'auth/recuperar';

// --- Dashboards por perfil ---
$route['painel'] = 'dashboard/index';
