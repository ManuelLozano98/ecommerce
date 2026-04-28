<?php

namespace App\Enums;

enum PaymentMethods: string
{
    case CREDIT_CARD = 'credit_card';
    case DEBIT_CARD = 'debit_card';
    case PAYPAL = 'paypal';
    case APPLE_PAY = 'apple_pay';
    case GOOGLE_PAY = 'google_pay';
    case CASH = 'cash';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
