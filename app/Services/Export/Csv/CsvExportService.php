<?php

namespace App\Services\Export\Csv;

use Closure;

class CsvExportService
{
    public function stream(CsvDefinition $definition, string $filename)
    {
        $callback = function () use ($definition): void {
            $out = fopen('php://output', 'w');
            // Excel で文字化けしないよう UTF-8 BOM を付与
            fwrite($out, pack('C*', 0xEF, 0xBB, 0xBF));
            fputcsv($out, $definition->headers());

            foreach ($definition->rows() as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        };

        return response()->streamDownload(
            $callback,
            $filename,
            ['Content-Type' => 'text/csv; charset=UTF-8']
        );
    }
}
