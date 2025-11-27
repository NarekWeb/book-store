<?php

namespace App\Services\Order;

use App\DTOs\OrderData;
use App\Models\Book;
use App\Models\BookPurchase;
use App\Models\BookRental;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Collection;

class PurchaseOrderProcessor implements OrderTypeProcessorInterface
{
    public function process(Order $order, OrderData $data, Collection $lockedBooks): void
    {
        /** @var Collection<int,Book> $lockedBooks */

        $grouped = collect($data->items)->groupBy('bookId')
            ->map(fn($items) => $items->sum('quantity'));

        foreach ($grouped as $bookId => $needQty) {
            /** @var Book|null $book */
            $book = $lockedBooks->get($bookId);
            if (!$book) {
                abort(422, "Book ID {$bookId} not found");
            }

            if ($book->available_copies < $needQty) {
                abort(422, "Not enough stock for book ID {$book->id}");
            }
        }

        $total = 0;

        foreach ($data->items as $itemData) {
            /** @var Book $book */
            $book = $lockedBooks[$itemData->bookId];

            if ($book->available_copies < $itemData->quantity) {
                abort(422, "Not enough stock for book ID {$book->id}");
            }

            $book->decrement('available_copies', $itemData->quantity);

            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'book_id' => $book->id,
                'quantity' => $itemData->quantity,
                'unit_price' => $itemData->unitPrice,
            ]);

            BookPurchase::create([
                'book_id' => $book->id,
                'order_id' => $order->id,
            ]);

            $total += $orderItem->quantity * $orderItem->unit_price;
        }

        $order->update(['total_amount' => $total]);
    }
}
