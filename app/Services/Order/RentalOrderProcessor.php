<?php

namespace App\Services\Order;

use App\DTOs\OrderData;
use App\Models\Book;
use App\Models\BookRental;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class RentalOrderProcessor implements OrderTypeProcessorInterface
{
    public function process(Order $order, OrderData $data, Collection $lockedBooks): void
    {
        /** @var Collection<int,Book> $lockedBooks */

        // 1. Групповая проверка наличия
        $grouped = collect($data->items)->groupBy('bookId')
            ->map(fn ($items) => $items->sum('quantity'));

        foreach ($grouped as $bookId => $needQty) {
            /** @var Book|null $book */
            $book = $lockedBooks->get($bookId);
            if (! $book) {
                abort(422, "Book ID {$bookId} not found");
            }

            if ($book->available_copies < $needQty) {
                abort(422, "Not enough copies for book ID {$book->id}");
            }
        }

        // 2. Создание позиций и записей аренды
        $total = 0;
        $rentedAt = Carbon::now();
        $dueDate  = $data->rentalDays
            ? $rentedAt->clone()->addDays($data->rentalDays)
            : null;

        foreach ($data->items as $itemData) {
            /** @var Book $book */
            $book = $lockedBooks[$itemData->bookId];

            if ($book->available_copies < $itemData->quantity) {
                abort(422, "Not enough copies for book ID {$book->id}");
            }

            $book->decrement('available_copies', $itemData->quantity);

            $orderItem = OrderItem::create([
                'order_id'   => $order->id,
                'book_id'    => $book->id,
                'quantity'   => $itemData->quantity,
                'unit_price' => $itemData->unitPrice,
            ]);

            BookRental::create([
                'order_item_id' => $orderItem->id,
                'book_id'       => $book->id,
                'user_id'       => $data->userId,
                'rented_at'     => $rentedAt,
                'due_date'      => $dueDate,
                'status'        => 'active',
            ]);

            $total += $orderItem->quantity * $orderItem->unit_price;
        }

        $order->update(['total_amount' => $total]);
    }
}

