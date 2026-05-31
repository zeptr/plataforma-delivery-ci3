<?php
defined('BASEPATH') OR exit('Acesso directo ao script não permitido');

/**
 * Pagamento_proc
 * ----------------------------------------------------------------------
 * Processador de pagamentos (MÓDULO 7). Simula a integração com os
 * métodos usados em Moçambique. Lógica pura e testável.
 *
 *   - mpesa    : carteira móvel Vodacom (exige número móvel)
 *   - emola    : carteira móvel Movitel (exige número móvel)
 *   - dinheiro : pago na entrega (fica pendente até confirmação)
 *   - cartao   : cartão bancário
 */
class Pagamento_proc
{
    private static $prefixos = [
        'mpesa'    => 'MPE',
        'emola'    => 'EMO',
        'dinheiro' => 'DIN',
        'cartao'   => 'CAR',
    ];

    /** Métodos de pagamento aceites pela plataforma. */
    public static function metodoValido($metodo)
    {
        return isset(self::$prefixos[$metodo]);
    }

    /**
     * Valida um número de telemóvel moçambicano.
     * Aceita formato nacional (8XXXXXXXX) ou internacional (2588XXXXXXXX).
     */
    public static function numeroMovelValido($numero)
    {
        $n = preg_replace('/\D+/', '', (string) $numero);
        $n = preg_replace('/^258/', '', $n);
        return (bool) preg_match('/^8[0-9]{8}$/', $n);
    }

    /**
     * Processa um pagamento e devolve o respectivo estado + referência.
     *
     * @return array{metodo:string,valor:float,estado:string,referencia:?string}
     * @throws InvalidArgumentException dados inválidos
     */
    public static function processar($metodo, $valor, $numeroMovel = '')
    {
        if (!self::metodoValido($metodo)) {
            throw new InvalidArgumentException("Método de pagamento inválido: {$metodo}");
        }
        if ((float) $valor <= 0) {
            throw new InvalidArgumentException('O valor do pagamento deve ser positivo.');
        }

        // Carteiras móveis exigem um número válido.
        if (in_array($metodo, ['mpesa', 'emola'], true) && !self::numeroMovelValido($numeroMovel)) {
            throw new InvalidArgumentException('Número de telemóvel inválido para o pagamento móvel.');
        }

        // "dinheiro" só é confirmado no acto da entrega.
        $estado = ($metodo === 'dinheiro') ? 'pendente' : 'pago';

        return [
            'metodo'     => $metodo,
            'valor'      => round((float) $valor, 2),
            'estado'     => $estado,
            'referencia' => self::gerarReferencia($metodo),
        ];
    }

    /** Gera uma referência única com o prefixo do método. */
    public static function gerarReferencia($metodo)
    {
        $prefixo = self::$prefixos[$metodo] ?? 'REF';
        return $prefixo . strtoupper(substr(md5(uniqid((string) mt_rand(), true)), 0, 9));
    }

    /** Rótulos legíveis para apresentação. */
    public static function rotulo($metodo)
    {
        $rotulos = [
            'mpesa'    => 'M-Pesa',
            'emola'    => 'e-Mola',
            'dinheiro' => 'Dinheiro na entrega',
            'cartao'   => 'Cartão bancário',
        ];
        return $rotulos[$metodo] ?? $metodo;
    }
}
