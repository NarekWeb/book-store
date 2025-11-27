<?php

namespace App\DTOs;

final class BookData
{
    public function __construct(
        public string  $title,
        public ?string $isbn,
        public int     $publishedYear,
        public int     $totalCopies,
        public int     $availableCopies,
        public array   $authorIds,
    )
    {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            isbn: $data['isbn'] ?? null,
            publishedYear: (int)$data['published_year'],
            totalCopies: (int)$data['total_copies'],
            availableCopies: (int)($data['available_copies'] ?? $data['total_copies']),
            authorIds: $data['author_ids'] ?? [],
        );
    }

    public function toModelAttributes(): array
    {
        return [
            'title' => $this->title,
            'isbn' => $this->isbn,
            'published_year' => $this->publishedYear,
            'total_copies' => $this->totalCopies,
            'available_copies' => $this->availableCopies,
        ];
    }

    public function toArray(): array
    {
        return $this->toModelAttributes() + [
                'author_ids' => $this->authorIds,
            ];
    }
}
