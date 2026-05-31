<?php
require_once __DIR__ . '/../../application/libraries/Estado_pedido.php';

/**
 * Testes da máquina de estados do pedido.
 * Fluxo: criado → recebido → confirmado → preparacao →
 *        entregador_aceite → saiu_entrega → entregue → finalizado
 * (com possibilidade de cancelamento até à preparação)
 */
class EstadoPedidoTest extends TestCase
{
    public function testEstadoInicialEhCriado(): void
    {
        $this->assertSame('criado', Estado_pedido::INICIAL);
    }

    public function testTodosOsOitoEstadosDoFluxoExistem(): void
    {
        $fluxo = ['criado', 'recebido', 'confirmado', 'preparacao',
                  'entregador_aceite', 'saiu_entrega', 'entregue', 'finalizado'];
        foreach ($fluxo as $estado) {
            $this->assertTrue(Estado_pedido::existe($estado), "Estado em falta: {$estado}");
        }
    }

    public function testTransicaoValidaSegueOFluxo(): void
    {
        $this->assertTrue(Estado_pedido::podeTransitar('criado', 'recebido'));
        $this->assertTrue(Estado_pedido::podeTransitar('recebido', 'confirmado'));
        $this->assertTrue(Estado_pedido::podeTransitar('confirmado', 'preparacao'));
        $this->assertTrue(Estado_pedido::podeTransitar('preparacao', 'entregador_aceite'));
        $this->assertTrue(Estado_pedido::podeTransitar('entregador_aceite', 'saiu_entrega'));
        $this->assertTrue(Estado_pedido::podeTransitar('saiu_entrega', 'entregue'));
        $this->assertTrue(Estado_pedido::podeTransitar('entregue', 'finalizado'));
    }

    public function testNaoPodeSaltarEstados(): void
    {
        $this->assertFalse(Estado_pedido::podeTransitar('criado', 'entregue'));
        $this->assertFalse(Estado_pedido::podeTransitar('criado', 'finalizado'));
        $this->assertFalse(Estado_pedido::podeTransitar('confirmado', 'entregue'));
    }

    public function testNaoPodeRetroceder(): void
    {
        $this->assertFalse(Estado_pedido::podeTransitar('confirmado', 'recebido'));
        $this->assertFalse(Estado_pedido::podeTransitar('entregue', 'preparacao'));
    }

    public function testPodeCancelarAntesDeSairParaEntrega(): void
    {
        $this->assertTrue(Estado_pedido::podeTransitar('criado', 'cancelado'));
        $this->assertTrue(Estado_pedido::podeTransitar('recebido', 'cancelado'));
        $this->assertTrue(Estado_pedido::podeTransitar('confirmado', 'cancelado'));
        $this->assertTrue(Estado_pedido::podeTransitar('preparacao', 'cancelado'));
    }

    public function testNaoPodeCancelarDepoisDeSairParaEntrega(): void
    {
        $this->assertFalse(Estado_pedido::podeTransitar('saiu_entrega', 'cancelado'));
        $this->assertFalse(Estado_pedido::podeTransitar('entregue', 'cancelado'));
        $this->assertFalse(Estado_pedido::podeTransitar('finalizado', 'cancelado'));
    }

    public function testEstadosTerminaisNaoTransitam(): void
    {
        $this->assertTrue(Estado_pedido::eTerminal('finalizado'));
        $this->assertTrue(Estado_pedido::eTerminal('cancelado'));
        $this->assertFalse(Estado_pedido::eTerminal('preparacao'));
        $this->assertCount(0, Estado_pedido::proximos('finalizado'));
        $this->assertCount(0, Estado_pedido::proximos('cancelado'));
    }

    public function testRotuloLegivelEmPortugues(): void
    {
        $this->assertEquals('Saiu para entrega', Estado_pedido::rotulo('saiu_entrega'));
        $this->assertEquals('Em preparação', Estado_pedido::rotulo('preparacao'));
    }

    public function testTransicaoDesconhecidaEhFalsa(): void
    {
        $this->assertFalse(Estado_pedido::podeTransitar('criado', 'inexistente'));
        $this->assertFalse(Estado_pedido::podeTransitar('inexistente', 'recebido'));
    }
}
