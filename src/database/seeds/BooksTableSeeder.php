<?php

namespace Database\Seeds;
use Illuminate\Database\Seeder;
use App\Book;

class BooksTableSeeder extends Seeder
{
    public function run()
    {
        Book::create([
            'title' => 'The Great Gatsby',
            'author' => 'F. Scott Fitzgerald',
        ]);

        Book::create([
            'title' => '1984',
            'author' => 'George Orwell',
        ]);
    }
}