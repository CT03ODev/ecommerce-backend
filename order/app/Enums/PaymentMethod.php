<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case VNPAY = 'vnpay';
    case COD = 'cod';

    public function label(): string
    {
        return match($this) {
            self::VNPAY => 'VNPay',
            self::COD => 'Cash on Delivery',
        };
    }
}