<?php
namespace App\Models\Enums;

abstract class PaymentOption
{
    const COD = 'cod';
    const STRIPE = 'stripe';

    public static $values = [
        self::COD => 'Cash On Delivery',
        self::STRIPE => 'Stripe Payment',
    ];
}
