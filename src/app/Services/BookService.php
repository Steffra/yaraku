<?php

namespace App\Services;

use App\Book;

class BookService
{

    public function getBooks(string $sortBy, string $sortOrder, string $authorFilter = '', string $titleFilter = ''): array
    {
        $query = Book::query();

        if ($authorFilter) {
            $query->where('author', 'like', '%' . $authorFilter . '%');
        }

        if ($titleFilter) {
            $query->where('title', 'like', '%' . $titleFilter . '%');
        }

        $books = $query->orderBy($sortBy, $sortOrder)->get();

        return $books->toArray();
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

    public function updateAuthorOfBook(int $id, string $author): Book
    {
        $book = Book::findOrFail($id);
        $book->author = $author;
        $book->save();
        return $book;
    }

}