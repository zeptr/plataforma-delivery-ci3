<?php
require_once __DIR__ . '/../../application/libraries/Carrinho_calc.php';

/**
 * Testes da calculadora do carrinho (lógica pura, sem sessão).
 * Garante totais correctos, agregação de quantidades e taxa de entrega.
 */
class CarrinhoTest extends TestCase
{
    private function itens(): array
    {
        return [
            ['produto_id' => 1, 'nome' => 'Pizza',  'preco' => 450.00, 'quantidade' => 2],
            ['produto_id' => 2, 'nome' => 'Sumo',   'preco' => 75.50,  'quantidade' => 3],
        ];
    }

    public function testSubtotalSomaPrecoVezesQuantidade(): void
    {
        // 450*2 + 75.5*3 = 900 + 226.5 = 1126.5
        $this->assertEquals(1126.50, Carrinho_calc::subtotal($this->itens()));
    }

    public function testTotalIncluiTaxaDeEntrega(): void
    {
        $this->assertEquals(1226.50, Carrinho_calc::total($this->itens(), 100.00));
    }

    public function testCarrinhoVazioTemSubtotalZero(): void
    {
        $this->assertEquals(0.0, Carrinho_calc::subtotal([]));
        $this->assertEquals(50.0, Carrinho_calc::total([], 50.00));
    }

    public function testAdicionarItemNovo(): void
    {
        $carrinho = [];
        $carrinho = Carrinho_calc::adicionar($carrinho, ['produto_id' => 5, 'nome' => 'Café', 'preco' => 60.0], 1);
        $this->assertCount(1, $carrinho);
        $this->assertEquals(1, $carrinho[5]['quantidade']);
    }

    public function testAdicionarItemExistenteAgregaQuantidade(): void
    {
        $carrinho = [];
        $produto = ['produto_id' => 5, 'nome' => 'Café', 'preco' => 60.0];
        $carrinho = Carrinho_calc::adicionar($carrinho, $produto, 1);
        $carrinho = Carrinho_calc::adicionar($carrinho, $produto, 2);
        $this->assertCount(1, $carrinho);
        $this->assertEquals(3, $carrinho[5]['quantidade']);
    }

    public function testActualizarQuantidade(): void
    {
        $carrinho = Carrinho_calc::adicionar([], ['produto_id' => 5, 'nome' => 'Café', 'preco' => 60.0], 1);
        $carrinho = Carrinho_calc::actualizar($carrinho, 5, 4);
        $this->assertEquals(4, $carrinho[5]['quantidade']);
    }

    public function testActualizarQuantidadeParaZeroRemoveItem(): void
    {
        $carrinho = Carrinho_calc::adicionar([], ['produto_id' => 5, 'nome' => 'Café', 'preco' => 60.0], 1);
        $carrinho = Carrinho_calc::actualizar($carrinho, 5, 0);
        $this->assertCount(0, $carrinho);
    }

    public function testRemoverItem(): void
    {
        $carrinho = Carrinho_calc::adicionar([], ['produto_id' => 5, 'nome' => 'Café', 'preco' => 60.0], 1);
        $carrinho = Carrinho_calc::adicionar($carrinho, ['produto_id' => 6, 'nome' => 'Pão', 'preco' => 10.0], 2);
        $carrinho = Carrinho_calc::remover($carrinho, 5);
        $this->assertCount(1, $carrinho);
        $this->assertFalse(isset($carrinho[5]));
    }

    public function testQuantidadeMinimaEhUm(): void
    {
        // Adicionar com quantidade inválida deve assumir 1.
        $carrinho = Carrinho_calc::adicionar([], ['produto_id' => 7, 'nome' => 'X', 'preco' => 5.0], 0);
        $this->assertEquals(1, $carrinho[7]['quantidade']);
    }

    public function testTotalDeArtigos(): void
    {
        $this->assertEquals(5, Carrinho_calc::totalArtigos($this->itens())); // 2 + 3
    }
}
