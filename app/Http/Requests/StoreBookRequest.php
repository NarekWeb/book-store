<?php

namespace App\Http\Requests;

use App\DTOs\BookData;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:20', 'unique:books,isbn'],
            'published_year' => ['nullable', 'integer', 'between:1500,' . date('Y')],
            'total_copies' => ['required', 'integer', 'min:0'],
            'available_copies' => ['nullable', 'integer', 'min:0'],
            'author_ids' => ['array'],
            'author_ids.*' => ['integer', 'exists:authors,id'],
        ];
    }

    public function toDto(): BookData
    {
        return new BookData(
            $this->input('title'),
            $this->input('isbn'),
            $this->input('published_year'),
            $this->input('total_copies'),
            $this->input('available_copies'),
            $this->input('author_ids'),
        );
    }
}
