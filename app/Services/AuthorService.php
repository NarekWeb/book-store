<?php

namespace App\Services;

use App\Models\Author;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AuthorService
{
    public function listAuthors(?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        $query = Author::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->paginate($perPage);
    }

    public function createAuthor(array $data): Author
    {
        return Author::create($data);
    }

    public function updateAuthor(Author $author, array $data): Author
    {
        $author->update($data);

        return $author;
    }

    public function deleteAuthor(Author $author): void
    {
        $author->delete();
    }
}
