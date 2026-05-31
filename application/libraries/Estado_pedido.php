<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/**
 * Estado_pedido
 * ----------------------------------------------------------------------
 * Máquina de estados do pedido. Lógica pura (sem dependências do
 * CodeIgniter) para ser facilmente testável e reutilizável.
 *
 * Fluxo operacional (MÓDULO 5):
 *   criado → recebido → confirmado → preparacao →
 *   entregador_aceite → saiu_entrega → entregue → finalizado
 *
 * O pedido pode ser "cancelado" enquanto ainda não saiu para entrega.
 */
class Estado_pedido
{
    const INICIAL = 'criado';

    /** Transições permitidas: estado actual => [estados seguintes válidos]. */
    private static $transicoes = [
        'criado'            => ['recebido', 'cancelado'],
        'recebido'          => ['confirmado', 'cancelado'],
        'confirmado'        => ['preparacao', 'cancelado'],
        'preparacao'        => ['entregador_aceite', 'cancelado'],
        'entregador_aceite' => ['saiu_entrega'],
        'saiu_entrega'      => ['entregue'],
        'entregue'          => ['finalizado'],
        'finalizado'        => [],
        'cancelado'         => [],
    ];

    /** Rótulos legíveis em Português. */
    private static $rotulos = [
        'criado'            => 'Pedido criado',
        'recebido'          => 'Recebido pela loja',
        'confirmado'        => 'Confirmado',
        'preparacao'        => 'Em preparação',
        'entregador_aceite' => 'Entregador atribuído',
        'saiu_entrega'      => 'Saiu para entrega',
        'entregue'          => 'Entregue',
        'finalizado'        => 'Finalizado',
        'cancelado'         => 'Cancelado',
    ];

    /** Verifica se um estado é conhecido pelo sistema. */
    public static function existe($estado)
    {
        return array_key_exists($estado, self::$transicoes);
    }

    /** Indica se a transição de $de para $para é permitida. */
    public static function podeTransitar($de, $para)
    {
        if (!self::existe($de) || !self::existe($para)) {
            return false;
        }
        return in_array($para, self::$transicoes[$de], true);
    }

    /** Devolve os estados para os quais $estado pode avançar. */
    public static function proximos($estado)
    {
        return self::existe($estado) ? self::$transicoes[$estado] : [];
    }

    /** Um estado é terminal se já não admite transições. */
    public static function eTerminal($estado)
    {
        return self::existe($estado) && empty(self::$transicoes[$estado]);
    }

    /** Rótulo legível em Português para apresentação ao utilizador. */
    public static function rotulo($estado)
    {
        return self::$rotulos[$estado] ?? ucfirst($estado);
    }

    /** Lista completa de estados na ordem do fluxo (sem 'cancelado'). */
    public static function fluxo()
    {
        return ['criado', 'recebido', 'confirmado', 'preparacao',
                'entregador_aceite', 'saiu_entrega', 'entregue', 'finalizado'];
    }

    /** Todos os estados com os respectivos rótulos (para selects/filtros). */
    public static function todos()
    {
        return self::$rotulos;
    }
}
