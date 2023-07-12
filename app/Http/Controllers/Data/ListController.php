<?php

namespace App\Http\Controllers\Data;

use App\Http\Controllers\Controller;
use App\Services\Data\DataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ListController extends Controller
{
    /**
     * Show the list of items for the given data definition.
     *
     * @param  string  $locale The current locale.
     * @param  string  $dataDefinitionName The name of the data definition to retrieve.
     * @param  Request  $request The incoming HTTP request.
     * @param  DataService  $dataService The data service to fetch the data definition.
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     *
     * @throws \Exception If the data definition is not found.
     */
    public function showList(string $locale, string $dataDefinitionName, Request $request, DataService $dataService)
    {
        // Find the data definition by name and instantiate it if it exists
        $dataDefinition = $dataService->findDataDefinitionByName($dataDefinitionName);
        if ($dataDefinition === null) {
            throw new \Exception('Data definition "'.$dataDefinitionName.'" not found');
        }
        $dataDefinition = new $dataDefinition;

        // Get unique ID for table
        $uniqueId = unique_code(12);

        // Retrieve settings
        $settings = $dataDefinition->getSettings([]);

        // Redirect to edit form, before checking if list view is allowed
        if($settings['redirectListToEdit'] && $settings['redirectListToEditColumn'] !== null) {
            $userId = auth($settings['guard'])->user()->id;
            $primaryKey = $dataDefinition->model->getKeyName();
            $item = $dataDefinition->model->select($primaryKey)->where($settings['redirectListToEditColumn'], $userId)->first();
            if ($item) {
                // Redirect to edit form
                $id = $item->{$primaryKey};
                return redirect()->route($settings['guard'].'.data.edit', ['name' => $dataDefinition->name, 'id' => $id]);
            } else {
                abort(404);
            }
        }

        // Abort if the list view is not allowed based on the settings
        if (! $settings['list']) {
            abort(404);
        }

        // Retrieve the table data for the data definition
        $tableData = $dataDefinition->getData($dataDefinition->name, 'list');

        // Determine the user type from the route name (member, staff, partner, or admin)
        // and verify if the Data Definition is permitted for that user type
        $guard = explode('.', $request->route()->getName())[0];
        if ($settings['guard'] !== $guard) {
            Log::notice('app\Http\Controllers\Data\ListController.php - View not allowed for '.$guard.', '.$settings['guard'].' required ('.auth($settings['guard'])->user()->email.')');
            abort(404);
        }

        // Return the view with the required data
        return view('data.list', compact('dataDefinition', 'uniqueId', 'settings', 'tableData'));
    }
}
