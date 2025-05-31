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

 
}
