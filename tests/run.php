<?php
/**
 * Executor de testes. Descobre todas as classes *Test em tests/unidade/,
 * corre cada método public que comece por "test" e reporta o resultado.
 *
 * Uso:  php tests/run.php
 */

error_reporting(E_ALL & ~E_DEPRECATED);

// Permite carregar as bibliotecas de domínio (que protegem contra acesso
// directo via BASEPATH) fora do ciclo normal do CodeIgniter.
if (!defined('BASEPATH')) {
    define('BASEPATH', __DIR__);
}

require __DIR__ . '/TestCase.php';

$dir = __DIR__ . '/unidade';
$ficheiros = glob($dir . '/*Test.php');
sort($ficheiros);

$antes = get_declared_classes();
foreach ($ficheiros as $f) {
    require $f;
}
$novas = array_diff(get_declared_classes(), $antes);

$totalTestes = 0;
$falhas = [];
$verde = "\033[32m";
$vermelho = "\033[31m";
$amarelo = "\033[33m";
$reset = "\033[0m";

echo "\n=== Suite de Testes — Plataforma de Delivery ===\n\n";

foreach ($novas as $classe) {
    if (!is_subclass_of($classe, 'TestCase')) {
        continue;
    }
    echo "» {$classe}\n";
    $metodos = array_filter(
        get_class_methods($classe),
        fn ($m) => strncmp($m, 'test', 4) === 0
    );

    foreach ($metodos as $metodo) {
        $totalTestes++;
        $instancia = new $classe();
        try {
            $instancia->setUp();
            $instancia->$metodo();
            echo "  {$verde}✓{$reset} {$metodo}\n";
        } catch (Throwable $e) {
            echo "  {$vermelho}✗ {$metodo}{$reset}\n";
            $falhas[] = [
                'classe' => $classe,
                'metodo' => $metodo,
                'erro'   => $e->getMessage(),
                'onde'   => $e->getFile() . ':' . $e->getLine(),
            ];
        }
    }
    echo "\n";
}

echo str_repeat('-', 55) . "\n";
$assert = TestCase::$assertions;

if (empty($falhas)) {
    echo "{$verde}OK{$reset} — {$totalTestes} testes, {$assert} asserções. Tudo passou.\n";
    exit(0);
}

echo "{$vermelho}FALHAS: " . count($falhas) . " de {$totalTestes} testes{$reset} ({$assert} asserções)\n\n";
foreach ($falhas as $i => $falha) {
    $n = $i + 1;
    echo "{$vermelho}{$n}) {$falha['classe']}::{$falha['metodo']}{$reset}\n";
    echo "   {$falha['erro']}\n";
    echo "   {$amarelo}{$falha['onde']}{$reset}\n\n";
}
exit(1);
