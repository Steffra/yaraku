<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Book;
use App\Services\BookService;

class BookController extends Controller
{

    protected $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    public function index()
    {
        $books = $this->bookService->getBooks();
        return response()->json($books);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
        ]);
        $book = $this->bookService->createBook($request->all());
        return response()->json($book, 201);
    }

}
