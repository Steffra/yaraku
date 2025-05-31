<?php

namespace App\Services;

use App\Book;

class BookService
{

    public function getBooks(): array
    {
        $books = Book::all();
        return ['data' => $books->toArray()];
    }
    
    public function createBook(array $data): Book
    {
        return Book::create($data);
    }

    public function deleteBook(int $id): void
    {
        $book = Book::findOrFail($id);
        $book->delete();
    }
}