<?php

namespace App\Http\Requests;

use App\DTOs\OrderData;
use App\Enums\OrderType;
use Illuminate\Foundation\Http\FormRequest;
use App\DTOs\OrderItemData;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(OrderType::class)],
            'user_id' => ['nullable', 'integer'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.book_id' => ['required', 'integer', 'exists:books,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'rental_days' => [Rule::requiredIf(fn() => $this->input('type') === OrderType::Rental->value), 'integer', 'min:1'],
        ];
    }


    public function toDto(): OrderData
    {
        $data = $this->validated();
        $items = array_map(
            fn(array $item) => new OrderItemData(
                (int)$item['book_id'],
                (int)$item['quantity'],
                (float)$item['unit_price'],
            ),
            $data['items'] ?? []
        );

        return new OrderData(
            $data['user_id'] ?? null,
            $data['type'],
            $items,
            $data['rental_days'] ?? null,
        );
    }


}
