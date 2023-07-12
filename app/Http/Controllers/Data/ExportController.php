<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use App\Services\Data\DataService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ExportController extends Controller
{
    /**
     * Export the list of items for the given data definition to a CSV.
     *
     * @param  string  $locale The current locale.
     * @param  string  $dataDefinitionName The name of the data definition to retrieve.
     * @param  Request  $request The incoming HTTP request.
     * @param  DataService  $dataService The data service to fetch the data definition.
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     *
     * @throws \Exception If the data definition is not found.
     */
    public function exportList(string $locale, string $dataDefinitionName, Request $request, DataService $dataService)
    {
        // Find the data definition by name and instantiate it if it exists
        $dataDefinition = $dataService->findDataDefinitionByName($dataDefinitionName);
        if ($dataDefinition === null) {
            throw new \Exception('Data definition "'.$dataDefinitionName.'" not found');
        }
        $dataDefinition = new $dataDefinition;

        // Get settings and data for the data definition
        $settings = $dataDefinition->getSettings([]);
        $tableData = $dataDefinition->getData($dataDefinition->name, 'export');

        // Obtain the user type from the route name (member, staff, partner, or admin) 
        // and verify if the Data Definition is permitted for that user type
        $guard = explode('.', $request->route()->getName())[0];
        if ($settings['guard'] !== $guard) {
            Log::notice('app\Http\Controllers\Data\ExportController.php - View not allowed for '.$guard.', '.$settings['guard'].' required ('.auth($settings['guard'])->user()->email.')');
            abort(404);
        }

        // Generate CSV file name
        $fileName = Str::slug($settings['title']. ' ' . date('Y-m-d H:i'), '-') . '.csv';

        // Prepare headers for the CSV file
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        // Extract CSV column names
        $columns = $tableData['columns'];
        $csvColumns = Arr::pluck($columns, 'text');

        // Define CSV content generation callback
        $callback = function() use($tableData, $csvColumns, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvColumns);

            foreach ($tableData['data'] as $columnName => $record) {
                foreach ($columns as $column) {
                    $row[$column['text']]  = $record->{$column['name']};
                }
                fputcsv($file, $row);
            }
            fclose($file);
        };

        // Stream the response as a CSV file
        return response()->stream($callback, 200, $headers);
    }
}
