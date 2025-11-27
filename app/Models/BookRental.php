<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookRental extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'book_id',
        'user_id',
        'rented_at',
        'due_date',
        'returned_at',
        'status',
    ];

    protected $casts = [
        'rented_at'   => 'datetime',
        'due_date'    => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
