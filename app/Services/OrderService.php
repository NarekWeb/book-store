<?php

namespace App\Services;

use App\DTOs\OrderData;
use App\Models\Book;
use App\Models\Order;
use App\Services\Order\OrderTypeProcessorInterface;
use App\Services\Order\PurchaseOrderProcessor;
use App\Services\Order\RentalOrderProcessor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private RentalOrderProcessor   $rentalProcessor,
        private PurchaseOrderProcessor $purchaseProcessor,
    )
    {
    }

    public function listOrders(int $perPage = 20): LengthAwarePaginator
    {
        return Order::with(['items.book'])->latest()->paginate($perPage);
    }

    public function createOrder(OrderData $orderDto): Order
    {
        return DB::transaction(function () use ($orderDto) {
            $order = Order::create([
                'user_id' => $orderDto->userId,
                'type' => $orderDto->type,
                'status' => 'completed',
                'total_amount' => 0,
            ]);

            $lockedBooks = $this->lockBooksForItems($orderDto);

            $processor = $this->resolveProcessor($orderDto->type);
            $processor->process($order, $orderDto, $lockedBooks);

            return $order->load('items.book');
        });
    }

    public function getOrder(Order $order): Order
    {
        return $order->load('items.book');
    }

    private function lockBooksForItems(OrderData $orderDto)
    {
        $bookIds = collect($orderDto->items)
            ->pluck('bookId')     // теперь это свойство реально есть в OrderItemData
            ->unique()
            ->sort()
            ->values();

        return Book::query()
            ->whereIn('id', $bookIds->all())
            ->lockForUpdate()
            ->get()
            ->keyBy('id');
    }



    private function resolveProcessor(string $type): OrderTypeProcessorInterface
    {
        return match ($type) {
            'rental' => $this->rentalProcessor,
            'purchase' => $this->purchaseProcessor,
            default => throw new \InvalidArgumentException("Unknown order type: {$type}"),
        };
    }
}
