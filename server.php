<?php
/**
 * Router para o servidor embutido do PHP (desenvolvimento sem XAMPP):
 *   php -S localhost:8080 server.php
 *
 * Serve ficheiros estáticos existentes (assets) directamente e encaminha
 * todo o restante tráfego para o front controller do CodeIgniter.
 */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$ficheiro = __DIR__ . $path;

if ($path !== '/' && is_file($ficheiro)) {
    return false; // o servidor embutido serve o ficheiro estático
}

require __DIR__ . '/index.php';
