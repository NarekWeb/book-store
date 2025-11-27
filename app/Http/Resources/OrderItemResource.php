<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'book'       => new BookResource($this->whenLoaded('book')),
            'quantity'   => $this->quantity,
            'unit_price' => $this->unit_price,
        ];
    }
}
