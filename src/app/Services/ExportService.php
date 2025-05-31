<?php

namespace App\Services;

use App\Services\Export\ExporterFactory;
use App\Book;

class ExportService
{
    public function export(string $type, array $fields)
    {
        $books = Book::all($fields)->toArray();

        $exporter = ExporterFactory::create($type);

        return $exporter->getExportContent($books, $fields);
    }
}
