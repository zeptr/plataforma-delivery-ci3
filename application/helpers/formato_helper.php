<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/**
 * Funções de formatação para Português de Moçambique:
 * moeda (Metical), datas e estados.
 */

if (!function_exists('moeda')) {
    /** Formata um valor em Meticais: 1 234,50 MT */
    function moeda($valor)
    {
        return number_format((float) $valor, 2, ',', ' ') . ' MT';
    }
}

if (!function_exists('data_pt')) {
    /** Formata uma data/hora ao estilo dd/mm/aaaa HH:MM. */
    function data_pt($datahora, $com_hora = true)
    {
        if (empty($datahora) || $datahora === '0000-00-00 00:00:00') {
            return '—';
        }
        $ts = is_numeric($datahora) ? $datahora : strtotime($datahora);
        return $com_hora ? date('d/m/Y H:i', $ts) : date('d/m/Y', $ts);
    }
}

if (!function_exists('estado_badge')) {
    /** Classe Bootstrap para o badge de cada estado do pedido. */
    function estado_badge($estado)
    {
        $mapa = [
            'criado'            => 'secondary',
            'recebido'          => 'info',
            'confirmado'        => 'primary',
            'preparacao'        => 'warning',
            'entregador_aceite' => 'primary',
            'saiu_entrega'      => 'info',
            'entregue'          => 'success',
            'finalizado'        => 'success',
            'cancelado'         => 'danger',
        ];
        return $mapa[$estado] ?? 'secondary';
    }
}
