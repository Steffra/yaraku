<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Export\ExporterFactory;
use App\Services\Export\CsvExporter;
use App\Services\Export\XmlExporter;

class ExporterFactoryTest extends TestCase
{
    public function test_it_creates_a_csv_exporter_instance()
    {
        $exporter = ExporterFactory::create('csv');

        $this->assertInstanceOf(CsvExporter::class, $exporter);
    }

    public function test_it_creates_an_xml_exporter_instance()
    {
        $exporter = ExporterFactory::create('xml');

        $this->assertInstanceOf(XmlExporter::class, $exporter);
    }

    public function test_it_throws_an_exception_for_invalid_export_type()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported export type: test');

        ExporterFactory::create('test');
    }
}