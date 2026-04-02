<?php

namespace App\Enums;

enum AccountType: string
{
    case ADMIN = 'admin';
    case CUSTOMER = 'customer';
    case VENDOR = 'vendor';
}
