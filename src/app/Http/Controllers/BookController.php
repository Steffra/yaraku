<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Book;
use App\Services\BookService;
use App\Services\ExportService;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{

    protected $bookService;
    protected $exportService;

    public function __construct(BookService $bookService, ExportService $exportService)
    {
        $this->bookService = $bookService;
        $this->exportService = $exportService;
    }

    public function index(Request $request)
    {
        $sortBy = $request->query('sortBy', 'id');
        $sortOrder = $request->query('sortOrder', 'asc'); 
        
        $author = $request->query('author', '');
        $title = $request->query('title', '');

        $validator = Validator::make($request->all(), [
            'sortBy' => 'in:id,title,author',
            'sortOrder' => 'in:asc,desc',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
    
        $books = $this->bookService->getBooks($sortBy, $sortOrder, $author, $title);
    
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

    public function export(Request $request, string $type)
    {
        $validator = Validator::make(array_merge($request->all(), ['type' => $type]), [
            'type' => 'required|in:csv,xml',
            'fields' => 'required|array',
            'fields.*' => 'in:author,title',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $fields = $request->input('fields');

        $content = $this->exportService->export($type, $fields);
        
        return response($content, 200, [
            'Content-Type' => $type === 'csv' ? 'text/csv' : 'application/xml',
            'Content-Disposition' => "attachment; filename=export." . $type,
        ]);
    }
}
