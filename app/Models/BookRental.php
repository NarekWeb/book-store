<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;

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

    public function scopeOverdue(Builder $query): Builder
    {
        return $query
            ->where('status', 'active')
            ->whereNull('returned_at')
            ->where('due_date', '<', now());
    }

}
