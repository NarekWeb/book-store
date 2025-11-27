<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'           => ['sometimes', 'string', 'max:255'],
            'isbn'            => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('books', 'isbn')->ignore($this->book),
            ],
            'published_year'  => ['nullable', 'integer', 'between:1500,' . date('Y')],
            'total_copies'    => ['sometimes', 'integer', 'min:0'],
            'available_copies'=> ['sometimes', 'integer', 'min:0'],
            'author_ids'      => ['sometimes', 'array'],
            'author_ids.*'    => ['integer', 'exists:authors,id'],
        ];
    }
}
