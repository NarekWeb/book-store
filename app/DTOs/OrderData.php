<?php

namespace App\DTOs;

final class OrderData
{
    public function __construct(
        public ?int  $userId,
        public string $type,
        /** @var OrderItemData[] */
        public array  $items,
        public ?int   $rentalDays,
    ) {
    }


//    public function fromArray(array $data): self
//    {
//        $dto->userId     = $data['user_id'] ?? null;
//        $dto->type       = $data['type'];
//        $dto->rentalDays = $data['rental_days'] ?? null;
//
//        $dto->items = array_map(
//            fn (array $item) => new OrderItemData(
//                (int) $item['book_id'],
//                (int) $item['quantity'],
//                (float) $item['unit_price'],
//            ),
//            $data['items'] ?? []
//        );
//
//        return $dto;
//    }

}
