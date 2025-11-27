<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct(
        protected BookService $bookService
    ) {}

    public function index(Request $request)
    {
        $search = $request->string('q')->toString();
        $books = $this->bookService->listBooks($search);

        return BookResource::collection($books);
    }

    public function store(StoreBookRequest $request)
    {
        $dto = $request->toDto();
        $book = $this->bookService->createBook($dto);

        return new BookResource($book);
    }

    public function show(Book $book)
    {
        $book = $book->load('authors');

        return new BookResource($book);
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        $book = $this->bookService->updateBook($book, $request->validated());

        return new BookResource($book);
    }

    public function destroy(Book $book)
    {
        $this->bookService->deleteBook($book);

        return response()->noContent();
    }
}
