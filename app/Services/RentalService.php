<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookRental;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class RentalService
{
    public function listRentals(?string $status = null, int $perPage = 20): LengthAwarePaginator
    {
        $query = BookRental::with('book');

        if ($status) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }

    /**
     * Возврат книги
     */
    public function returnRental(BookRental $rental): BookRental
    {
        if ($rental->status !== 'active') {
            return $rental;
        }

        DB::transaction(function () use ($rental) {
            $rental->update([
                'status'      => 'returned',
                'returned_at' => now(),
            ]);

            /** @var Book $book */
            $book = $rental->book()->lockForUpdate()->first();
            $book->increment('available_copies', 1);
        });

        return $rental->fresh('book');
    }
}
