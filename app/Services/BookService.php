<?php

namespace App\Services;

use App\DTOs\BookData;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookService
{
    public function listBooks(?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        $query = Book::query()->with('authors');

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        return $query->paginate($perPage);
    }

    public function createBook(BookData $data): Book
    {
        $book = Book::create($data->toModelAttributes());

        if (!empty($data['author_ids'])) {
            $book->authors()->sync($data['author_ids']);
        }

        return $book->load('authors');
    }

    public function updateBook(Book $book, array $data): Book
    {
        $book->update($data);

        if (array_key_exists('author_ids', $data)) {
            $book->authors()->sync($data['author_ids'] ?? []);
        }

        return $book->load('authors');
    }

    public function deleteBook(Book $book): void
    {
        $book->delete();
    }
}
