<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/**
 * Carrinho_calc
 * ----------------------------------------------------------------------
 * Lógica pura do carrinho de compras (MÓDULO 4). Opera sobre arrays
 * indexados pelo produto_id, sem depender da sessão — o que a torna
 * facilmente testável. A biblioteca Carrinho (com sessão) delega aqui.
 *
 * Estrutura de um item:
 *   [produto_id => ['produto_id'=>int,'nome'=>str,'preco'=>float,'quantidade'=>int]]
 */
class Carrinho_calc
{
    /** Adiciona um produto; se já existir, soma a quantidade. */
    public static function adicionar(array $carrinho, array $produto, $quantidade = 1)
    {
        $id = (int) $produto['produto_id'];
        $quantidade = max(1, (int) $quantidade);

        if (isset($carrinho[$id])) {
            $carrinho[$id]['quantidade'] += $quantidade;
        } else {
            $carrinho[$id] = [
                'produto_id' => $id,
                'nome'       => $produto['nome'],
                'preco'      => (float) $produto['preco'],
                'quantidade' => $quantidade,
            ];
        }
        return $carrinho;
    }

    /** Define a quantidade de um item. Quantidade <= 0 remove o item. */
    public static function actualizar(array $carrinho, $produtoId, $quantidade)
    {
        $id = (int) $produtoId;
        $quantidade = (int) $quantidade;

        if (!isset($carrinho[$id])) {
            return $carrinho;
        }
        if ($quantidade <= 0) {
            return self::remover($carrinho, $id);
        }
        $carrinho[$id]['quantidade'] = $quantidade;
        return $carrinho;
    }

    /** Remove um item do carrinho. */
    public static function remover(array $carrinho, $produtoId)
    {
        unset($carrinho[(int) $produtoId]);
        return $carrinho;
    }

    /** Soma de preço × quantidade de todos os itens. */
    public static function subtotal(array $carrinho)
    {
        $total = 0.0;
        foreach ($carrinho as $item) {
            $total += (float) $item['preco'] * (int) $item['quantidade'];
        }
        return round($total, 2);
    }

    /** Subtotal + taxa de entrega. */
    public static function total(array $carrinho, $taxaEntrega = 0.0)
    {
        return round(self::subtotal($carrinho) + (float) $taxaEntrega, 2);
    }

    /** Número total de artigos (soma das quantidades). */
    public static function totalArtigos(array $carrinho)
    {
        $n = 0;
        foreach ($carrinho as $item) {
            $n += (int) $item['quantidade'];
        }
        return $n;
    }
}
