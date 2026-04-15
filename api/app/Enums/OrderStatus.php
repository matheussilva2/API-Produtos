<?php

namespace App\Enums;

enum OrderStatus: string {
    case CRIADO = 'CRIADO';
    case PAGO = 'PAGO';
    case CANCELADO = 'CANCELADO';

    public static function values(): array {
        return array_column(self::cases(), 'value');
    }
}