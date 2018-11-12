<?php
declare(strict_types=1);

namespace Itineris\SRMImporter;

class UploadMimes
{
    public static function allowCSV(array $mimes): array
    {
        $mimes['csv'] = 'text/csv';
        return $mimes;
    }
}
