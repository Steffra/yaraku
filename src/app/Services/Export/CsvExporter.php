<?php

namespace App\Services\Export;

class CsvExporter implements ExporterInterface
{
    public function getExportContent(array $books): string
    {
        $csvContent = implode(',', array_keys($books[0])) . "\n";

        foreach ($books as $book) {
            $row = [];
            foreach ($book as $field => $value) {
                $row[] = str_replace('"', '""', $value);
            }
            $csvContent .= '"' . implode('","', $row) . "\"\n";
        }

        return $csvContent;
    }
}