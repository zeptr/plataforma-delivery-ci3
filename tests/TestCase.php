<?php
/**
 * Mini-framework de testes (estilo xUnit) sem dependências externas.
 * Suficiente para Test Driven Development do domínio da plataforma.
 */

class AssertionFailed extends Exception {}

abstract class TestCase
{
    public static int $assertions = 0;

    protected function fail(string $mensagem): void
    {
        throw new AssertionFailed($mensagem);
    }

    protected function assertTrue($condicao, string $msg = ''): void
    {
        self::$assertions++;
        if ($condicao !== true) {
            $this->fail($msg ?: 'Esperava TRUE, obteve ' . var_export($condicao, true));
        }
    }

    protected function assertFalse($condicao, string $msg = ''): void
    {
        self::$assertions++;
        if ($condicao !== false) {
            $this->fail($msg ?: 'Esperava FALSE, obteve ' . var_export($condicao, true));
        }
    }

    protected function assertEquals($esperado, $obtido, string $msg = ''): void
    {
        self::$assertions++;
        if ($esperado != $obtido) {
            $this->fail($msg ?: "Esperava " . var_export($esperado, true) . ", obteve " . var_export($obtido, true));
        }
    }

    protected function assertSame($esperado, $obtido, string $msg = ''): void
    {
        self::$assertions++;
        if ($esperado !== $obtido) {
            $this->fail($msg ?: "Esperava (===) " . var_export($esperado, true) . ", obteve " . var_export($obtido, true));
        }
    }

    protected function assertCount(int $esperado, $array, string $msg = ''): void
    {
        self::$assertions++;
        $n = is_countable($array) ? count($array) : -1;
        if ($n !== $esperado) {
            $this->fail($msg ?: "Esperava $esperado elementos, obteve $n");
        }
    }

    protected function assertNull($valor, string $msg = ''): void
    {
        self::$assertions++;
        if ($valor !== null) {
            $this->fail($msg ?: 'Esperava NULL, obteve ' . var_export($valor, true));
        }
    }

    protected function assertNotNull($valor, string $msg = ''): void
    {
        self::$assertions++;
        if ($valor === null) {
            $this->fail($msg ?: 'Esperava valor não-nulo, obteve NULL');
        }
    }

    /** Executa $callback e confirma que lança uma excepção (opcionalmente do tipo dado). */
    protected function assertThrows(callable $callback, string $classe = Throwable::class, string $msg = ''): void
    {
        self::$assertions++;
        try {
            $callback();
        } catch (Throwable $e) {
            if (!($e instanceof $classe)) {
                $this->fail($msg ?: "Esperava excepção {$classe}, obteve " . get_class($e));
            }
            return;
        }
        $this->fail($msg ?: "Esperava que fosse lançada uma excepção {$classe}, mas nada foi lançado");
    }

    /** Gancho opcional executado antes de cada teste. */
    public function setUp(): void {}
}
