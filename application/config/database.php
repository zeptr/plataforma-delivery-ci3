<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| LIGAÇÃO À BASE DE DADOS — Plataforma de Delivery
| -------------------------------------------------------------------
| Configurações para MySQL (XAMPP). Pode definir variáveis de ambiente
| para sobrepor os valores por omissão sem editar este ficheiro:
|   DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT
*/

$active_group = (getenv('CI_ENV') === 'testing') ? 'testing' : 'default';
$query_builder = TRUE;

// Durante a instalação (/instalar) a base de dados pode ainda não existir.
// Nesse caso desligamos o "db_debug" para que a página do instalador carregue
// mesmo sem ligação — o próprio instalador trata de criar tudo via mysqli.
$a_instalar = isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'instalar') !== false;
$db_debug_padrao = (ENVIRONMENT !== 'production') && !$a_instalar;
// Durante a instalação ligamos ao servidor sem seleccionar base de dados
// (que pode ainda não existir), evitando avisos de "Unknown database".
$bd_padrao = $a_instalar ? '' : (getenv('DB_NAME') ?: 'delivery_mz');

$db['default'] = array(
	'dsn'      => '',
	'hostname' => getenv('DB_HOST') ?: 'localhost',
	'username' => getenv('DB_USER') ?: 'root',
	'password' => getenv('DB_PASS') ?: '',
	'database' => $bd_padrao,
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => $db_debug_padrao,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8mb4',
	'dbcollat' => 'utf8mb4_unicode_ci',
	'swap_pre' => '',
	'encrypt'  => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE,
	'port'     => (int) (getenv('DB_PORT') ?: 3306),
);

// Grupo usado pelos testes automatizados (base de dados isolada).
$db['testing'] = array(
	'dsn'      => '',
	'hostname' => getenv('DB_HOST') ?: 'localhost',
	'username' => getenv('DB_USER') ?: 'root',
	'password' => getenv('DB_PASS') ?: '',
	'database' => getenv('DB_NAME_TEST') ?: 'delivery_mz_test',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => TRUE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8mb4',
	'dbcollat' => 'utf8mb4_unicode_ci',
	'swap_pre' => '',
	'encrypt'  => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE,
	'port'     => (int) (getenv('DB_PORT') ?: 3306),
);
