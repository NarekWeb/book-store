<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookPurchase extends Model
{
    protected $fillable = [
        'book_id',
        'order_id',
    ];
}
