<?php

namespace App\Enums;

enum SaleStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';
    case REFUNDED = 'refunded';
    case FAILED = 'failed';
    
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}
