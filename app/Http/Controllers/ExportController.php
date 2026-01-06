<?php

namespace App\Http\Controllers;

use App\Services\Export\Csv\CsvExportService;
use App\Services\Export\Csv\Definitions\PostDailySummariesCsvDefinition;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function dailySummaries(Request $request, CsvExportService $csvExportService)
    {
        $definition = new PostDailySummariesCsvDefinition(
            $request->query('from'),
            $request->query('to')
        );

        return $csvExportService->stream($definition, 'post_daily_summaries.csv');
    }
}
