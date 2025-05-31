<?php

namespace App\Services\Export;

class ExporterFactory
{
    public static function create(string $type): ExporterInterface
    {
        switch ($type) {
            case 'csv':
                return new CsvExporter();
            case 'xml':
                return new XmlExporter();
            default:
                throw new \InvalidArgumentException("Unsupported export type: $type");
        }
    }
}