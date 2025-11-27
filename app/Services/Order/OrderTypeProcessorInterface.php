<?php

namespace App\Services\Order;

use App\DTOs\OrderData;
use App\Models\Book;
use App\Models\Order;
use Illuminate\Support\Collection;

interface OrderTypeProcessorInterface
{
    /**
     * @param Order $order
     * @param OrderData $data
     * @param Collection<int,Book> $lockedBooks
     */
    public function process(Order $order, OrderData $data, Collection $lockedBooks): void;
}
