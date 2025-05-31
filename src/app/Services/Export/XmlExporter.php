<?php

namespace App\Services\Export;

class XmlExporter implements ExporterInterface
{
    public function getExportContent (array $books): string
    {
        $xmlContent = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<books>\n";
        foreach ($books as $book) {
            $xmlContent .= "  <book>\n";
            foreach ($book as $field => $value) {
                $xmlContent .= "    <$field>" . htmlspecialchars($value) . "</$field>\n";
            }
            $xmlContent .= "  </book>\n";
        }

        $xmlContent .= "</books>";
        return $xmlContent;
    }
}