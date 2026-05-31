<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/**
 * Funções de segurança reutilizáveis: hashing de palavras-passe e
 * escape de saída para prevenir XSS.
 */

if (!function_exists('hash_password')) {
    /** Gera um hash seguro da palavra-passe (bcrypt). */
    function hash_password($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}

if (!function_exists('verificar_password')) {
    /** Verifica uma palavra-passe contra o respectivo hash. */
    function verificar_password($password, $hash)
    {
        return password_verify($password, $hash);
    }
}

if (!function_exists('e')) {
    /** Escapa texto para saída segura em HTML (anti-XSS). */
    function e($texto)
    {
        return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('token_aleatorio')) {
    /** Gera um token aleatório (ex.: recuperação de palavra-passe). */
    function token_aleatorio($tamanho = 32)
    {
        return bin2hex(random_bytes($tamanho / 2));
    }
}
