<?php

namespace App\Enums;

enum OrderType: string
{
    case Purchase = 'purchase';
    case Rental = 'rental';
}
