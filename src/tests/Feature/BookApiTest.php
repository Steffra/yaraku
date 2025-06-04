<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Book;
use Database\Seeds\BooksTableSeeder;
class BookApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->artisan('db:seed');
    }    

    public function test_it_can_list_all_books()
    {
        $response = $this->get('/api/books');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonFragment(['title' => 'The Great Gatsby']);
        $response->assertJsonFragment(['author' => 'George Orwell']);
    }
    
    public function test_it_can_filter_and_sort_books()
    {
        $response = $this->get('/api/books?author=George');
        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['title' => '1984']);
        $response->assertJsonFragment(['author' => 'George Orwell']);

        $response = $this->get('/api/books?title=Great');
        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['title' => 'The Great Gatsby']);

        $response = $this->get('/api/books?sortBy=title&sortOrder=asc');
        $response->assertStatus(200);
        $books = $response->json();
        $this->assertEquals('1984', $books[0]['title']);
        $this->assertEquals('The Great Gatsby', $books[1]['title']);

        $response = $this->get('/api/books?sortBy=title&sortOrder=desc');
        $response->assertStatus(200);
        $books = $response->json();
        $this->assertEquals('The Great Gatsby', $books[0]['title']);
        $this->assertEquals('1984', $books[1]['title']);
    }

    public function test_it_can_create_a_new_book()
    {
        $response = $this->post('/api/books', [
            'title' => 'To Kill a Mockingbird',
            'author' => 'Harper Lee',
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['title' => 'To Kill a Mockingbird']);
        $response->assertJsonFragment(['author' => 'Harper Lee']);

        $this->assertDatabaseHas('books', [
            'title' => 'To Kill a Mockingbird',
            'author' => 'Harper Lee',
        ]);
    }

    public function test_it_can_update_an_existing_book()
    {
        $book = Book::first();

        $response = $this->put("/api/books/{$book->id}", [
            'author' => 'Updated Author',
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['author' => 'Updated Author']);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'author' => 'Updated Author',
        ]);
    }

    public function test_it_can_delete_a_book()
    {
        $book = Book::first();

        $response = $this->delete("/api/books/{$book->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }

    public function test_it_can_export_books_as_csv()
    {
        $response = $this->get('/api/books/export/csv?fields[]=title&fields[]=author');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename=export.csv');

        $expectedCsv = <<<CSV
title,author
"The Great Gatsby","F. Scott Fitzgerald"
"1984","George Orwell"

CSV;

        $this->assertEquals($expectedCsv, $response->getContent());
    }

    public function test_it_can_export_books_as_xml()
    {
        $response = $this->get('/api/books/export/xml?fields[]=title&fields[]=author');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');
        $response->assertHeader('Content-Disposition', 'attachment; filename=export.xml');

        $expectedXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<books>
  <book>
    <title>The Great Gatsby</title>
    <author>F. Scott Fitzgerald</author>
  </book>
  <book>
    <title>1984</title>
    <author>George Orwell</author>
  </book>
</books>
XML;

        $this->assertEquals($expectedXml, $response->getContent());
    }

    public function test_it_returns_validation_errors_when_sorting_books_with_invalid_data()
    {
        $response = $this->get('/api/books?sortBy=invalid&sortOrder=asc');

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'error' => [
                'sortBy' => ['The selected sort by is invalid.'],
            ],
        ]);

        $response = $this->get('/api/books?sortBy=title&sortOrder=invalid');

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'error' => [
                'sortOrder' => ['The selected sort order is invalid.'],
            ],
        ]);
    }
    
    public function test_it_returns_validation_errors_when_creating_a_book_with_invalid_data()
    {
        $response = $this->postJson('/api/books', [
            'title' => '', 
                'author' => '', 
            ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'errors' => [
                'title' => ['The title field is required.'],
                'author' => ['The author field is required.'],
            ],
        ]);
    }

    public function test_it_returns_an_error_when_updating_a_non_existent_book()
    {
        $response = $this->put('/api/books/999', [
            'author' => 'Updated Author',
        ]);

        $response->assertStatus(422);
    }

    public function test_it_returns_an_error_when_deleting_a_non_existent_book()
    {
        $response = $this->delete('/api/books/999');

        $response->assertStatus(422); 
    }

    public function test_it_returns_validation_errors_when_exporting_books_with_invalid_data()
    {
        $response = $this->get('/api/books/export/invalid?fields[]=title&fields[]=author');

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'error' => [
                'type' => ['The selected type is invalid.'],
            ],
        ]);

        $response = $this->get('/api/books/export/csv');

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'error' => [
                'fields' => ['The fields field is required.'],
            ],
        ]);
    }


}