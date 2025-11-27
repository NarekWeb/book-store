<?php

namespace App\DTOs;

final class OrderItemData
{
    public function __construct(
        public int   $bookId,
        public int   $quantity,
        public float $unitPrice,
    )
    {
    }
}
