<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Book;
use App\Services\BookService;
use Illuminate\Support\Facades\Validator;

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

    public function destroy($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:books,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $this->bookService->deleteBook($id);

        return response()->json(null, 204);
    } 

    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'author' => 'required|string|max:255',
        ]);

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:books,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $book = $this->bookService->updateAuthorOfBook($id, $request->input('author'));

        return response()->json($book, 200);
    }
}
