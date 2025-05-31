<?php

namespace App\Services\Export;

interface ExporterInterface
{
    public function getExportContent(array $books): string;
}