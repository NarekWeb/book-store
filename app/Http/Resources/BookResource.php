<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'isbn'             => $this->isbn,
            'published_year'   => $this->published_year,
            'total_copies'     => $this->total_copies,
            'available_copies' => $this->available_copies,
            'authors'          => AuthorResource::collection($this->whenLoaded('authors')),
        ];
    }
}
