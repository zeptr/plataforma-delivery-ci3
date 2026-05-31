<?php
require_once __DIR__ . '/../../application/libraries/Pagamento_proc.php';

/**
 * Testes do processador de pagamentos.
 * Métodos suportados em Moçambique: M-Pesa, e-Mola, Dinheiro, Cartão.
 */
class PagamentoTest extends TestCase
{
    public function testMetodosSuportados(): void
    {
        $this->assertTrue(Pagamento_proc::metodoValido('mpesa'));
        $this->assertTrue(Pagamento_proc::metodoValido('emola'));
        $this->assertTrue(Pagamento_proc::metodoValido('dinheiro'));
        $this->assertTrue(Pagamento_proc::metodoValido('cartao'));
        $this->assertFalse(Pagamento_proc::metodoValido('bitcoin'));
    }

    public function testDinheiroFicaPendenteAteEntrega(): void
    {
        $r = Pagamento_proc::processar('dinheiro', 500.0, '258840000000');
        $this->assertSame('pendente', $r['estado']);
    }

    public function testMpesaExigeNumeroMovel(): void
    {
        $this->assertThrows(
            fn () => Pagamento_proc::processar('mpesa', 500.0, ''),
            'InvalidArgumentException'
        );
    }

    public function testMpesaComNumeroValidoFicaPago(): void
    {
        $r = Pagamento_proc::processar('mpesa', 500.0, '258841234567');
        $this->assertSame('pago', $r['estado']);
        $this->assertNotNull($r['referencia']);
    }

    public function testReferenciaTemPrefixoDoMetodo(): void
    {
        $r = Pagamento_proc::processar('emola', 300.0, '258861234567');
        $this->assertSame('EMO', substr($r['referencia'], 0, 3));
        $m = Pagamento_proc::processar('mpesa', 300.0, '258841234567');
        $this->assertSame('MPE', substr($m['referencia'], 0, 3));
    }

    public function testValorTemDeSerPositivo(): void
    {
        $this->assertThrows(
            fn () => Pagamento_proc::processar('dinheiro', 0, ''),
            'InvalidArgumentException'
        );
        $this->assertThrows(
            fn () => Pagamento_proc::processar('dinheiro', -10, ''),
            'InvalidArgumentException'
        );
    }

    public function testNumeroMovelMocambicano(): void
    {
        // Operadoras móveis MZ: 82/83 (Vodacom→M-Pesa via 84/85), 86/87 (Movitel→e-Mola)
        $this->assertTrue(Pagamento_proc::numeroMovelValido('840000000'));
        $this->assertTrue(Pagamento_proc::numeroMovelValido('258851234567'));
        $this->assertFalse(Pagamento_proc::numeroMovelValido('123'));
        $this->assertFalse(Pagamento_proc::numeroMovelValido('abc'));
    }
}
