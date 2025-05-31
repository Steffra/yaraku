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

    public function test_it_can_list_all_books()
    {
        $response = $this->get('/api/books');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonFragment(['title' => 'The Great Gatsby']);
        $response->assertJsonFragment(['author' => 'George Orwell']);
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
}