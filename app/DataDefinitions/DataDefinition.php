<?php

namespace App\DataDefinitions;

use App\View\Components\Ui\Icon;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravolt\Avatar\Facade as Avatar;
use App\Models\Member;
use App\Models\Staff;
use App\Models\Partner;
use App\Models\Admin;
use App\Models\Affiliate;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DataDefinition
{
    /**
     * Retrieve data.
     *
     * @param  string  $dateDefinitionName The date definition name
     * @param  string  $dateDefinitionView The date definition view
     * @param  array  $options The options array
     * @param  Model  $model The model instance
     * @param  array  $settings The settings array
     * @param  array  $fields The fields array
     * @return array The columns and data
     */
    public function getData(string $dateDefinitionName, string $dateDefinitionView, array $options, Model $model, array $settings, array $fields): array
    {
        // Get settings
        $settings = $this->getSettings($settings);

        // Get options
        $id = $options['id'] ?? null;

        // Check if there is a filter for a certain user role
        if ($settings['userFilterRole'] && isset($settings['userFilterRole'][auth($settings['guard'])->user()->role])) {
            // Get filtered data
            $filter = $settings['userFilterRole'][auth($settings['guard'])->user()->role];
            $query = $filter($model);
        } else {
            // Get data
            $query = $model->query();
        }

        // General query filter
        if ($settings['queryFilter']) {
            // Apply $query queryFilter
            $query = $settings['queryFilter']($query);
        }

        // Primary key
        $primaryKey = $model->getKeyName();
        $query->addSelect("{$primaryKey} as id");

        if ($model->schemaHasColumn('is_undeletable')) {
            $query->addSelect('is_undeletable');
        }
        if ($model->schemaHasColumn('is_uneditable')) {
            $query->addSelect('is_uneditable');
        }

        // Only access to record(s) created by current user
        if ($settings['userMustOwnRecords']) {
            $user_id = auth($settings['guard'])->user()->id;
            $query->where('created_by', $user_id);
        }

        // Add columns to query
        [$columns, $appends, $relations, $filters, $tabs] = $this->addColumnsToQuery($fields, $dateDefinitionView, $model, $query, $settings);

        // The "insert" view does not need data, so we return a new model instance
        if ($dateDefinitionView === 'insert') {
            $data = new $model;
        } elseif ($dateDefinitionView === 'view') {
            $eloquentObject = $query->where($primaryKey, $id);
            $data = $eloquentObject->first();

            // Process the record that will be shown in the view
            $processedData = $this->processRows($eloquentObject->get(), $columns, $dateDefinitionName, $settings)[0] ?? [];

            foreach ($processedData as $columnName => $value) {
                $data->{$columnName} = $value;
            }
        } elseif ($dateDefinitionView === 'export') {
            $data = $query->get();

            // Process the record that will be shown in the view
            $processedData = $this->processRows($data, $columns, $dateDefinitionName, $settings)[0] ?? [];
            foreach ($processedData as $columnName => $value) {
                $data->{$columnName} = $value;
            }
        } elseif ($id === null) {
            // Get search
            $searchTerm = request()->get('search');

            if ($searchTerm) {
                $this->addSearchConditions($query, $columns, $searchTerm);
            }

            // Add filter(s)
            if (request()->input('filter')) {
                foreach (request()->input('filter') as $columnName => $id) {
                    $query->where($columnName, $id);
                }
            }

            // Pagination
            $currentPage = request()->input('page', 1);
            $itemsPerPage = $settings['itemsPerPage'];
            $recordsToRetrieve = $itemsPerPage * ($currentPage + 1);

            // Retrieve the records
            $allRecords = $query;

            if ($settings['orderRelation']) {
                $allRecords = $allRecords->selectSub($settings['orderRelation'], 'sub_order')
                    ->orderBy('sub_order', $settings['orderDirection']);
            } else {
                $allRecords = $allRecords->orderBy($settings['orderByColumn'], $settings['orderDirection']);
            }

            // General query filter
            if ($settings['queryFilter']) {
                $allRecords = $settings['queryFilter']($allRecords);
            }

            $totalRecords = $allRecords->count();

            $allRecords = $allRecords->take($recordsToRetrieve)->get();            

            $currentPageRecords = $allRecords->slice(($currentPage - 1) * $itemsPerPage, $itemsPerPage);

            /*
            $resultsWithAppends = $currentPageRecords->map(function ($item) use ($appends) {
                foreach ($appends as $append) {
                    // Force the accessor to run and include the appended attribute in the toArray() results
                    $item->setAttribute($append, $item->{$append});
                }
                return $item;
            })->toArray();
            */

            $hasMorePages = $allRecords->count() > ($currentPage * $itemsPerPage);

            // Process the records that will be shown in the view
            $processedRows = $this->processRows($currentPageRecords, $columns, $dateDefinitionName, $settings);

            // Add processed records to paginator
            $data = new LengthAwarePaginator($processedRows, $totalRecords, $itemsPerPage, $currentPage, [
                'path' => route($settings['guard'].'.data.list', ['name' => $dateDefinitionName]),
                'hasMorePagesWhen' => $hasMorePages,
            ]);

            // Keep other query string parameters
            $data->appends(request()->except('page'));
        } else {
            $data = $query->where($primaryKey, $id)->first();

            if (! Str::endsWith($dateDefinitionView, '.post') && $data !== null) {
                // Process relations
                foreach ($relations as $relation) {
                    $data->{$relation['column']} = $data->{$relation['column']}()->pluck($relation['key'])->toArray();
                }
            }
        }

        return [
            'columns' => $columns,
            'data' => $data,
            'relations' => $relations,
            'filters' => $filters,
            'tabs' => $tabs,
        ];
    }

    /**
     * Add columns to the query.
     *
     * @param  array  $fields The fields to process
     * @param  string  $dateDefinitionView The date definition view
     * @param  Model  $model The model instance
     * @param  Builder  $query The query builder instance
     * @param  array  $settings The settings array
     * @return array The columns, appends and relations
     */
    private function addColumnsToQuery(array $fields, string $dateDefinitionView, Model $model, $query, array $settings): array
    {
        $columns = [];
        $appends = [];
        $relations = [];
        $filters = [];
        $tabs = [];

        // Flatten array in case of tabs
        $fieldsFlatten = [];
        foreach ($fields as $columnName => $field) {
            // Check if this is a tab
            if (preg_match('/^tab\d/', $columnName)) {
                $tab = $columnName;
                $tabs[$tab] = $field['title'];
                foreach ($field['fields'] as $columnName => $field) {
                    $field['tab'] = $tab;
                    $fieldsFlatten[$columnName] = $field;
                }
            } else {
                $fieldsFlatten[$columnName] = $field;
            }
        }

        foreach ($fieldsFlatten as $columnName => $field) {
            // Check if the column should be included in the query
            $actionsContainView = isset($field['actions']) && is_array($field['actions']) && in_array($dateDefinitionView, $field['actions']);
            if ($actionsContainView || Str::contains($columnName, '::')) {
                $existsInDatabase = false;
                $skip = false;

                // Remove ::view from column name if present, otherwise skip this iteration
                if (Str::contains($columnName, '::')) {
                    if (Str::endsWith($columnName, '::'.$dateDefinitionView)) {
                        $columnName = Str::replaceLast('::'.$dateDefinitionView, '', $columnName);
                    } else {
                        $skip = true;
                    }
                }

                if (! $skip) {
                    // Add column to select statement
                    if ($model->schemaHasColumn($columnName)) {
                        $query->addSelect($columnName);
                        $existsInDatabase = true;
                    } elseif (in_array($columnName, $model->getAppends())) {
                        $appends[] = $columnName;
                    } elseif (isset($field['sql'])) {
                        $query->addSelect(DB::raw($field['sql'].' as '.$columnName));
                    }

                    $format = ($field['type'] == 'string') ? 'text' : null;

                    // Prepare column data
                    $columnData = [
                        'exists_in_database' => $existsInDatabase,
                        'name' => $columnName,
                        'text' => $field['text'] ?? 'Column',
                        'default' => $field['default'] ?? null,
                        'allowHtml' => $field['allowHtml'] ?? false,
                        'placeholder' => $field['placeholder'] ?? null,
                        'json' => $field['json'] ?? null, // The value is a json column where the value of the data is stored
                        'prefix' => $field['prefix'] ?? null,
                        'suffix' => $field['suffix'] ?? null,
                        'type' => $field['type'] ?? 'string',
                        'translatable' => $field['translatable'] ?? false,
                        'generatePasswordButton' => $field['generatePasswordButton'] ?? false,
                        'mailUserPassword' => $field['mailUserPassword'] ?? false,
                        'mailUserPasswordChecked' => $field['mailUserPasswordChecked'] ?? false,
                        'min' => $field['min'] ?? null,
                        'max' => $field['max'] ?? null,
                        'step' => $field['step'] ?? null,
                        'relation' => $field['relation'] ?? null,
                        'relationKey' => $field['relationKey'] ?? null,
                        'relationValue' => $field['relationValue'] ?? null,
                        'relationModel' => $field['relationModel'] ?? null,
                        'relationMustBeOwned' => $field['relationMustBeOwned'] ?? false,
                        'relationFilter' => $field['relationFilter'] ?? null,
                        'relationUserRoleFilter' => $field['relationUserRoleFilter'] ?? null,
                        'relationThrough' => $field['relationThrough'] ?? null,
                        'relationThroughPivot' => $field['relationThroughPivot'] ?? null,
                        'relationThroughValue' => $field['relationThroughValue'] ?? null,
                        'relationThroughOrderByColumn' => $field['relationThroughOrderByColumn'] ?? null,
                        'relationThroughOrderDirection' => $field['relationThroughOrderDirection'] ?? 'desc',
                        'relationThroughModel' => $field['relationThroughModel'] ?? null,
                        'relationThroughFilter' => $field['relationThroughFilter'] ?? null,
                        'relationThroughLink' => $field['relationThroughLink'] ?? null,
                        'query' => $field['query'] ?? null,
                        'textualAvatarBasedOnColumn' => $field['textualAvatarBasedOnColumn'] ?? null,
                        'titleColumn' => $field['titleColumn'] ?? null,
                        'format' => $field['format'] ?? $format,
                        'highlight' => $field['highlight'] ?? false,
                        'filter' => $field['filter'] ?? false,
                        'hidden' => $field['hidden'] ?? false,
                        'options' => $field['options'] ?? null,
                        'searchable' => $field['searchable'] ?? false,
                        'sortable' => $field['sortable'] ?? false,
                        'validate' => $field['validate'] ?? ['nullable'],
                        'guard' => $field['guard'] ?? null,
                        'help' => $field['help'] ?? null,
                        'url' => $field['url'] ?? null,
                        'classes::list' => $field['classes::list'] ?? null,
                        'classes::insert' => $field['classes::insert'] ?? null,
                        'container_start::insert' => $field['container_start::insert'] ?? null,
                        'container_end::insert' => $field['container_end::insert'] ?? false,
                        'classes::edit' => $field['classes::edit'] ?? null,
                        'container_start::edit' => $field['container_start::edit'] ?? null,
                        'container_end::edit' => $field['container_end::edit'] ?? false,
                        'classes::view' => $field['classes::view'] ?? null,
                        'container_start::view' => $field['container_start::view'] ?? null,
                        'container_end::view' => $field['container_end::view'] ?? false,
                        'accept' => $field['accept'] ?? null, // Image/file upload e.g "image/svg+xml, image/png, image/jpeg, image/gif"
                        'thumbnail' => $field['thumbnail'] ?? null, // Image conversion used for list
                        'conversion' => $field['conversion'] ?? null, // Image conversion used for view/edit
                        'tab' => $field['tab'] ?? null,
                    ];

                    // Process relations
                    if ($columnData['relationKey'] && $columnData['relationValue'] && $columnData['relationModel'] instanceof Model) {
                        // Set options based on related model and add column
                        if (in_array($columnData['type'], ['belongsToMany'])) {
                            // Check if there is a filter for a certain user role
                            if ($columnData['relationUserRoleFilter'] && isset($columnData['relationUserRoleFilter'][auth($settings['guard'])->user()->role])) {
                                // Get filtered options
                                $filter = $columnData['relationUserRoleFilter'][auth($settings['guard'])->user()->role];

                                if ($columnData['relationMustBeOwned']) {
                                    $columnData['options'] = $filter($columnData['relationModel']::where('created_by', auth($settings['guard'])->user()->id))
                                        ->pluck($columnData['relationValue'], $columnData['relationKey'])
                                        ->toArray();
                                } else {
                                    $columnData['options'] = $filter($columnData['relationModel'])->pluck($columnData['relationValue'], $columnData['relationKey'])->toArray();
                                }
                            } else {
                                // Get options
                                if ($columnData['relationMustBeOwned']) {
                                    $columnData['options'] = $columnData['relationModel']::where('created_by', auth($settings['guard'])->user()->id)
                                        ->pluck($columnData['relationValue'], $columnData['relationKey'])
                                        ->toArray();
                                } else {
                                    $columnData['options'] = $columnData['relationModel']->pluck($columnData['relationValue'], $columnData['relationKey'])->toArray();
                                }
                            }

                            // Add column
                            $query->with([$columnData['relation'] => function ($q) use ($columnData) {
                                $q->select([$columnData['relationValue']]);
                            }]);

                            $relations[] = [
                                'type' => $columnData['type'],
                                'column' => $columnName,
                                'key' => $columnData['relationKey'],
                                'value' => $columnData['relationValue']
                            ];
                        }

                        // Set options list based on related model
                        if ($columnData['type'] == 'belongsTo') {
                            // Check if there is a filter for a certain user role
                            if ($columnData['relationUserRoleFilter'] && isset($columnData['relationUserRoleFilter'][auth($settings['guard'])->user()->role])) {
                                // Get filtered options
                                $filter = $columnData['relationUserRoleFilter'][auth($settings['guard'])->user()->role];

                                if ($columnData['relationMustBeOwned']) {
                                    $columnData['options'] = $filter($columnData['relationModel']::where('created_by', auth($settings['guard'])->user()->id))->pluck($columnData['relationValue'], $columnData['relationKey'])->toArray();
                                } else {
                                    $columnData['options'] = $filter($columnData['relationModel'])->pluck($columnData['relationValue'], $columnData['relationKey'])->toArray();
                                }
                            } else {
                                // Get options
                                if ($columnData['relationMustBeOwned']) {
                                    $columnData['options'] = $columnData['relationModel']::where('created_by', auth($settings['guard'])->user()->id)
                                        ->pluck($columnData['relationValue'], $columnData['relationKey'])
                                        ->toArray();
                                } else {
                                    $columnData['options'] = $columnData['relationModel']->pluck($columnData['relationValue'], $columnData['relationKey'])->toArray();
                                }
                            }
                        }

                        // Process filters
                        if ($columnData['filter']) {
                            $filters[$columnName] = [
                                'text' => $columnData['text'],
                                'options' => $columnData['options']
                            ];
                        }
                    }

                    // Process json arrays
                    if ($columnData['json']) {
                        if (DB::getDriverName() === 'sqlite') {
                            $query->addSelect(DB::raw("CASE WHEN json_valid({$columnData['json']}) THEN json_extract({$columnData['json']}, '$.$columnName') END as $columnName"));
                        } else {
                            $query->addSelect(DB::raw("CASE WHEN {$columnData['json']} IS NOT NULL THEN JSON_UNQUOTE(JSON_EXTRACT({$columnData['json']}, '$.$columnName')) END as $columnName"));
                        }
                    }                    

                    // Add column to the columns array
                    $columns[$columnName] = $columnData;
                }
            }
        }

        return [$columns, $appends, $relations, $filters, $tabs];
    }

    /**
     * Loop through rows and set default values and formatting.
     *
     * @param  \Illuminate\Contracts\Pagination\CursorPaginator  $rows The rows to process
     * @param  array  $columns The columns array
     * @param  string  $dateDefinitionName The date definition name
     * @param  array  $settings The settings array
     * @return array The processed rows
     */
    private function processRows($rows, array $columns, string $dateDefinitionName, array $settings): array
    {
        $processedRows = [];
        foreach ($rows as $row) {
            $processedColumns['id'] = $row->id;
            foreach ($columns as $column) {
                $value = $row->{$column['name']} ?? $column['default'];

                // Image
                if ($column['type'] === 'image' && $value !== null) {
                    $value = $this->processImageColumn($column, $row);
                }

                // Avatar
                if ($column['type'] === 'avatar') {
                    $value = $this->processAvatarColumn($column, $row);
                }

                // Boolean
                if ($column['type'] === 'boolean') {
                    $value = $this->processBooleanColumn($column, $value);
                }

                // Number
                if ($column['type'] === 'number' && $value !== null) {
                    $value = '<span class="format-number">'.$value.'</span>';
                }

                // Date time
                if ($column['type'] === 'date_time' && $value !== null) {
                    $value = $this->processDateTimeColumn($column, $row, $value);
                }

                // Language / locale
                if ($column['type'] === 'locale') {
                    $value = $this->processLocaleColumn($column, $row, $value);
                }

                // Time zone
                if ($column['type'] === 'time_zone') {
                    $value = $this->processTimeZoneColumn($column, $row, $value);
                }

                // Currency
                if ($column['type'] === 'currency') {
                    $value = $this->processCurrencyColumn($column, $row, $value);
                }

                // User
                if (Str::startsWith($column['type'], 'user.')) {
                    $value = $this->processUserColumn($column, $row, $value, $settings);
                }

                // Select
                if ($column['type'] === 'select') {
                    $value = $this->processSelectColumn($column, $row, $value, $settings);
                }

                // Relations
                if (in_array($column['type'], ['belongsToMany', 'belongsTo', 'manyToMany'])) {
                    $value = $this->processRelationColumn($column, $row, $value, $settings);
                }

                // Impersonate as user, log in to account
                if ($column['type'] === 'impersonate') {
                    $value = $this->processImpersonateColumn($dateDefinitionName, $column, $row, $settings);
                }

                // QR code with link
                if ($column['type'] === 'qr') {
                    $value = $this->processQrColumn($dateDefinitionName, $column, $row, $settings);
                }

                // Query
                if ($column['type'] === 'query') {
                    $value = $this->processQueryColumn($column, $row, $settings);
                }

                // Formatting is done after getting values
                if ($column['format'] === 'number' && $value !== null) {
                    $value = '<span class="format-number">'.$value.'</span>';
                }

                // Format email
                if ($column['format'] === 'hideEmail') {
                    $value = hideEmailAddress($value ?? '');
                }

                $processedColumns[$column['name']] = $value ?? '';
            }
            $processedRows[] = $processedColumns;
        }

        return $processedRows;
    }

    /**
     * Process the image column for the given row.
     *
     * @param  array  $column The column configuration array
     * @param  object  $row The row object containing the data for the image column
     * @return string The formatted image HTML string
     */
    private function processImageColumn(array $column, $row): string
    {
        if ($row->{$column['name']} !== null && $column['thumbnail'] !== null) {
            $value = $row->{$column['name'].'-'.$column['thumbnail']};
        }
        $value = '<img src="'.$value.'" class="mx-auto rounded-lg shadow-lg">';

        return $value;
    }

    /**
     * Process the avatar column for the given row.
     *
     * @param  array  $column The column configuration array
     * @param  object  $row The row object containing the data for the avatar column
     * @return string The formatted avatar HTML string
     */
    private function processAvatarColumn(array $column, $row): string
    {
        if (! $row->{$column['name']} && $column['textualAvatarBasedOnColumn'] !== null) {
            $value = Avatar::create($row->{$column['textualAvatarBasedOnColumn']})->toBase64();
        } elseif ($row->{$column['name']} !== null && $column['thumbnail'] !== null) {
            $value = $row->{$column['name'].'-'.$column['thumbnail']};
        }
        $value = '<img src="'.$value.'" class="w-10 h-10 mx-auto rounded-full">';

        return $value;
    }

    /**
     * Process and format the boolean column value based on the given configuration.
     *
     * @param  array  $column The column configuration array
     * @param  mixed  $value The boolean value to be formatted
     * @return string The formatted boolean value as a string
     */
    private function processBooleanColumn(array $column, $value): string
    {
        if ($column['format'] == 'text') {
            $value = ($value)
                ? trans('common.yes')
                : trans('common.no');
        } elseif ($column['format'] == 'icon') {
            $iconComponent = new Icon(($value) ? 'check' : 'x-mark', 'w-5 h-5');
            $value = $iconComponent->render()->render(); // The second render() call is to render the View object to a string
        } else {
            $value = ($value)
                ? '<div class="flex items-center"><div class="w-4 h-4 bg-green-500 rounded-full" style="background-color: rgb(14, 159, 110);"></div></div>'
                : '<div class="flex items-center"><div class="w-4 h-4 bg-red-500 rounded-full" style="background-color: rgb(240, 82, 82);"></div></div>';
        }

        return $value;
    }

    /**
     * Process and format the date time column value based on the given configuration.
     *
     * @param  array  $column The column configuration array
     * @param  mixed  $row The row containing the date time value
     * @param  mixed  $value The boolean value to be formatted
     * @return string The formatted date time value as a string, or an empty string if the value is null
     */
    private function processDateTimeColumn(array $column, $row, $value): string
    {
        if ($row->{$column['name']} !== null) {
            $timeZone = app()->make('i18n')->time_zone;
            $dateTime = new Carbon($value, 'UTC'); // Set the source timezone to UTC
            $dateTime = $dateTime->timezone($timeZone)->format('Y-m-d H:i:s');

            return $dateTime;
        } else {
            return (string) $column['default'];
        }
    }

    /**
     * Process and format the language (locale) column value based on the given configuration.
     *
     * @param  array  $column The column configuration array
     * @param  mixed  $row The row containing the date time value
     * @param  mixed  $value The boolean value to be formatted
     * @return string The formatted date time value as a string, or an empty string if the value is null
     */
    private function processLocaleColumn(array $column, $row, $value): string
    {
        if ($row->{$column['name']} === null) {
            $value = $column['default'];
        } else {
            $value = $row->{$column['name']};
        }

        $i18nService = app(\App\Services\I18nService::class);
        $value = $i18nService->getLocaleName($column['default']);

        return $value;
    }

    /**
     * Process and format the time zone column value based on the given configuration.
     *
     * @param  array  $column The column configuration array
     * @param  mixed  $row The row containing the date time value
     * @param  mixed  $value The boolean value to be formatted
     * @return string The formatted date time value as a string, or an empty string if the value is null
     */
    private function processTimeZoneColumn(array $column, $row, $value): string
    {
        if ($row->{$column['name']} === null) {
            $value = (string) $column['default'];
        }

        return str_replace('_', ' ', $value);
    }

    /**
     * Process and format the currency column value based on the given configuration.
     *
     * @param  array  $column The column configuration array
     * @param  mixed  $row The row containing the date time value
     * @param  mixed  $value The boolean value to be formatted
     * @return string The formatted date time value as a string, or an empty string if the value is null
     */
    private function processCurrencyColumn(array $column, $row, $value): string
    {
        if ($row->{$column['name']} === null) {
            $value = (string) $column['default'];
            if ($value !== '') {
                $language = app()->make('i18n')->language;
                $i18nService = resolve('App\Services\I18nService');
                $currency = $i18nService->getCurrencyDetails($value);
                $currencyName = $currency['name'][$language->current->languageCode] ?? $currency['name']['en'];
                $value = $currencyName.' ('.$currency['id'].')';
            }
        } else {
            $language = app()->make('i18n')->language;
            $i18nService = resolve('App\Services\I18nService');
            $currency = $i18nService->getCurrencyDetails($value);
            $currencyName = $currency['name'][$language->current->languageCode] ?? $currency['name']['en'];
            $value = $currencyName.' ('.$currency['id'].')';
        }

        return $value;
    }

    /**
     * Process and format a user column value based on the given configuration.
     *
     * @param  array  $column The column configuration array
     * @param  mixed  $row The row containing the date time value
     * @param  mixed  $value The boolean value to be formatted
     * @param  array  $settings The settings array
     * @return string The formatted user value as a string, or the default value if the value is null or user not found
     *
     * @throws Exception If the user type does not exist
     */
    private function processUserColumn(array $column, $row, $value, array $settings): string
    {
        // Early return if the row value is null
        if ($row->{$column['name']} === null) {
            return (string) $column['default'];
        }

        // Define a mapping of user types to their corresponding models
        $userTypeToModelMap = [
            'member' => Member::class,
            'staff' => Staff::class,
            'partner' => Partner::class,
            'admin' => Admin::class,
            'affiliate' => Affiliate::class,
        ];

        $type = explode('.', $column['type'])[1];

        if (!array_key_exists($type, $userTypeToModelMap)) {
            throw new \Exception('User type does not exist');
        }

        // Fetch the corresponding user based on type
        $user = $userTypeToModelMap[$type]::find($value);

        // If user is null, return default
        if ($user === null) {
            return (string) $column['default'];
        }

        // Format the value using user name and email, fall back to email if name is null
        return $user->name !== null ? "{$user->name} ({$user->email})" : $user->email;
    }

    /**
     * Process and format a select column value based on the given configuration.
     *
     * @param  array  $column The column configuration array
     * @param  mixed  $row The row containing the date time value
     * @param  mixed  $value The boolean value to be formatted
     * @param  array  $settings The settings array
     * @return string The formatted user value as a string, or an empty string if the value is null
     */
    private function processSelectColumn(array $column, $row, $value, array $settings): string
    {
        if ($row->{$column['name']} === null) {
            $value = (string) $column['default'];
        } else {
            $value = ($column['options'] !== null && isset($column['options'][$value])) ? $column['options'][$value] : (string) $column['default'];
        }

        return $value;
    }

    /**
     * Process and format a select relation column value based on the given configuration.
     *
     * @param  array  $column The column configuration array
     * @param  mixed  $row The row containing the date time value
     * @param  mixed  $value The boolean value to be formatted
     * @param  array  $settings The settings array
     * @return string The formatted user value as a string, or an empty string if the value is null
     */
    private function processRelationColumn(array $column, $row, $value, array $settings): string
    {
        // Default value
        $value = (string) $column['default'];

        // Process relations
        if ($row->{$column['name']} !== null && $column['relationKey'] && $column['relationValue']) {
            if (in_array($column['type'], ['belongsToMany'])) {
                $value = $row->{$column['relation']}()->pluck($column['relationValue'])->toArray();
                $value = implode(', ', $value);
            }
            if ($column['type'] == 'belongsTo') {
                $value = $row->{$column['relation']}()->pluck($column['relationValue'])->first();
            }
        } elseif ($column['type'] == 'manyToMany' && $column['relationThroughPivot'] && $column['relationThroughValue']) {
            $filter = $column['relationThroughFilter'];
            $query = $row->{$column['relationThrough']}()
                        ->with($column['relationThroughPivot']) // eager load relation
                        ->where(function($query) use ($filter) {
                            $filter($query);
                        });

            if ($column['relationThroughOrderByColumn']) {
                $query->orderBy($column['relationThroughOrderByColumn'], $column['relationThroughOrderDirection']);
            }

            $values = $query->get()
                        ->map(function ($q) use($column) {
                            return $q->{$column['relationThroughPivot']}->{$column['relationThroughValue']};
                        })
                        ->filter() // remove null values
                        ->unique() // filter out duplicate values
                        ->toArray();

            if ($column['relationThroughLink']) {
                $parsedValues = [];
                $uniqueValues = [];
                foreach ($query->get() as $relationThrough) {
                    if ($relationThrough->{$column['relationThroughPivot']}) {
                        $id = $relationThrough->{$column['relationThroughPivot']}->id;
                        $value = $relationThrough->{$column['relationThroughPivot']}->{$column['relationThroughValue']};

                        // Check if the value is already processed, if yes then continue the loop
                        if (in_array($id, $uniqueValues)) {
                            continue;
                        }

                        $link = $column['relationThroughLink']($row, $column, $relationThrough);
                        $parsedValues[] = '<a href="'.$link.'" class="text-link">'.$value.'</a>';
            
                        // Record the processed value
                        $uniqueValues[] = $id;
                    }
                }
                $value = implode(', ', $parsedValues);
            } else {
                $value = implode(', ', $values);
            }
        }
        return $value;
    }

    /**
     * Create an impersonate link for the given row based on the column configuration.
     *
     * @param  string  $dateDefinitionName The name of the date definition
     * @param  array  $column The column configuration array
     * @param  mixed  $row The row containing the impersonate data
     * @param  array  $settings The settings array
     * @return string The impersonate link as a formatted HTML string
     */
    private function processImpersonateColumn(string $dateDefinitionName, array $column, $row, array $settings): string
    {
        $iconComponent = new Icon('key', 'h-3.5 w-3.5');
        $icon = $iconComponent->render()->render(); // The second render() call is to render the View object to a string

        $value = '<a href="'.route($settings['guard'].'.data.impersonate', ['name' => $dateDefinitionName, 'guard' => $column['guard'], 'id' => $row->id]).'" data-fb="tooltip" title="'.trans('common.log_in_to_account').'" class="inline-flex items-center whitespace-nowrap btn-dark btn-xs p-2">'.$icon.'</a>';

        return $value;
    }

    /**
     * Create an qr link for the given row based on the column configuration.
     *
     * @param  string  $dateDefinitionName The name of the date definition
     * @param  array  $column The column configuration array
     * @param  mixed  $row The row containing the impersonate data
     * @param  array  $settings The settings array
     * @return string The qr as a formatted HTML string
     */
    private function processQrColumn(string $dateDefinitionName, array $column, $row, array $settings): string
    {
        $iconComponent = new Icon('qr-code', 'h-5 w-5');
        $icon = $iconComponent->render()->render(); // The second render() call is to render the View object to a string

        $id = $row->id;
        $title = ($column['titleColumn']) ? $row->{$column['titleColumn']} : trans('common.loyalty_card');
        $closeBtn = trans('common.close');
        $url = str_replace(':id', $id, $column['url']);
        $qr = QrCode::size(300)
            ->format('svg')
            ->errorCorrection('M')
            ->generate($url);

        $content = '<div class="bg-white p-4 rounded-lg shadow"><a href="'.$url.'" target="_blank">'.$qr.'</a></div><div class="mt-4"><a class="text-link" href="'.$url.'" target="_blank">'.$url.'</a></div>';

$script = <<<EOD
<script>
document.getElementById('qr-$id').addEventListener('click', (event) => {
    event.preventDefault();

    const opts = {
        title: `$title`,
        btnClose: { text: `$closeBtn` }
    };
    window.appAlert (`$content`, opts);
    console.log("$url");
});
</script>
EOD;

        $value = '<a href="javascript:void(0);" id="qr-'.$id.'" class="inline-flex items-center whitespace-nowrap btn-dark btn-xs p-1">'.$icon.'</a>' . $script;

        return $value;
    }

    /**
     * Execute the query callback of the column on the row if present, convert the result to a string and return.
     * If the 'query' key is not present or null, the default value of the column is returned.
     *
     * @param  array  $column An array representing the column settings. Should contain a 'query' key that is a callable.
     * @param  mixed  $row The current row that is processed.
     * @param  array  $settings The settings of the table, not used in the current implementation.
     * @return  string  The result of the query, converted to a string, or the default value from the column settings.
     */
    private function processQueryColumn(array $column, $row, array $settings): string
    {
        // Extract the query function from the column array
        $queryFunction = $column['query'] ?? null;

        // Check if the query function exists
        if ($queryFunction) {
            // Call the function on the $row, the result might not be a string
            $result = $queryFunction($row);

            // Convert the result to a string, if it's not already a string
            // strval() is a PHP function that can convert various types to string.
            // The ternary operator is used here to check if the result is already a string,
            // if it is, it will be returned as is, otherwise it will be converted.
            $value = is_string($result) ? $result : strval($result);
        } else {
            // If the query function does not exist, return the default value from the column settings
            $value = $column['default'] ?? '';
        }

        // Return the result, or default value if result is null
        return $value;
    }

    /**
     * Merge and validate the provided settings array with default values.
     *
     * @param  array  $settings The user-defined settings array
     * @return array The merged and validated settings array
     */
    public function getSettings(array $settings): array
    {
        // Per row actions
        $edit = isset($settings['actions']) && ($settings['actions']['edit'] ?? false);
        $delete = isset($settings['actions']) && ($settings['actions']['delete'] ?? false);
        $view = isset($settings['actions']) && ($settings['actions']['view'] ?? false);
        $hasActions = $edit || $delete || $view;

        // General actions
        $list = isset($settings['actions']) && ($settings['actions']['list'] ?? false);
        $insert = isset($settings['actions']) && ($settings['actions']['insert'] ?? false);
        $export = isset($settings['actions']) && ($settings['actions']['export'] ?? false);
        $subject_column = (isset($settings['actions']) && isset($settings['actions']['subject_column']))
            ? $settings['actions']['subject_column'] : false;

        $settings = [
            // Query filter
            'queryFilter' => $settings['queryFilter'] ?? null,
            // Icon
            'icon' => $settings['icon'] ?? null,
            // Title (plural of subject)
            'title' => $settings['title'] ?? 'Items',
            // Override title (this title is shown in every view, without sub title like "Edit item" or "View item")
            'overrideTitle' => $settings['overrideTitle'] ?? null,
            // Description
            'description' => $settings['description'] ?? null,
            // Guard of user that manages this data (member, staff, partner or admin)
            // This guard is also used for routes and to include the correct Blade layout
            'guard' => $settings['guard'] ?? 'admin',
            // The guard used for sending the user password
            'mailUserPasswordGuard' => $settings['mailUserPasswordGuard'] ?? null,
            // If set, these role(s) are required
            'roles' => $settings['roles'] ?? null,
            // Used for updating forms like profile, where user has to enter current password in order to save
            // Keep in mind that there is no check whether the logged in user actually "owns" the record,
            // for that check 'redirectListToEdit' is required
            'editRequiresPassword' => $settings['editRequiresPassword'] ?? false,
            // If true, the visitor is redirected to the edit form. This can be used for editing a user profile.
            'redirectListToEdit' => $settings['redirectListToEdit'] ?? false,
            // This column has to match auth($guard)->user()->id if 'redirectListToEdit' == true (usually it will be 'id' or 'created_by')
            // This is also validated on save
            'redirectListToEditColumn' =>  $settings['redirectListToEditColumn'] ?? null,
            // If true, the user id must match the created_by field
            'userMustOwnRecords' => $settings['userMustOwnRecords'] ?? true,
            // Filter with certain role
            'userFilterRole' =>  $settings['userFilterRole'] ?? null,
            // Should there be checkboxes for all rows (e.g. to delete multiple records with one action)
            'multiSelect' => $settings['multiSelect'] ?? false,
            // Default items per page for pagination
            'itemsPerPage' => $settings['itemsPerPage'] ?? 20,
            // Default order by column
            'orderByColumn' => request()->input('order', null) ?? $settings['orderByColumn'] ?? 'id',
            // Default order direction, 'asc' or 'desc'
            'orderDirection' =>request()->input('orderDir', null) ?? $settings['orderDirection'] ?? 'asc',
            // Order by relation
            'orderRelation' =>  $settings['orderRelation'] ?? null,
            // Possible actions for the data
            'edit' => $edit,
            'delete' => $delete,
            // This column is used for page titles and delete confirmations
            'subject_column' => $subject_column,
            'view' => $view,
            // Is list view allowed?
            'list' => $list,
            'insert' => $insert,
            'export' => $export,
            'hasActions' => $hasActions,
        ];

        // Verify if user role is required and matches
        if (is_array($settings['roles']) && ! in_array(auth($settings['guard'])->user()->role, $settings['roles'])) {
            Log::notice('app\DataDefinitions\DataDefinition.php - User ('.auth($settings['guard'])->user()->email.') does not have required role ('.implode(', ', $settings['roles']).')');
            abort(404);
        }

        return $settings;
    }

    /**
     * Apply search conditions to the query based on provided columns and search term.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query  The query builder instance
     * @param  array  $columns  The list of column definitions
     * @param  string  $searchTerm  The search term to apply to the query
     */
    private function addSearchConditions($query, array $columns, string $searchTerm): void
    {
        $firstColumn = true;
        foreach ($columns as $column) {
            if ($column['searchable']) {
                if ($firstColumn) {
                    $query->where($column['name'], 'LIKE', '%'.$searchTerm.'%');
                    $firstColumn = false;
                } else {
                    $query->orWhere($column['name'], 'LIKE', '%'.$searchTerm.'%');
                }
            }
        }
    }
}
