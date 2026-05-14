<?php

namespace App\Enums;

namespace App\Enums;

enum PaymentMethod: string
{
    case STRIPE = 'stripe';
    case PAYPAL = 'paypal';
    case CASH_ON_DELIVERY = 'cod';
    case BANK_TRANSFER = 'bank_transfer';
}
