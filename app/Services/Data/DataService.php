<?php

namespace App\Services\Data;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Notifications\Member\Registration;
use Carbon\Carbon;
use HTMLPurifier;
use HTMLPurifier_Config;

class DataService
{
    /**
     * Find a data definition by its name.
     *
     * @param  string  $dataDefinitionName The name of the data definition to find.
     * @return object|null Returns the data definition model if found, otherwise null.
     */
    public function findDataDefinitionByName($dataDefinitionName)
    {
        // Obtain the user type from the route name (member, staff, partner, or admin)
        $classDir = explode('.', request()->route()->getName())[0];
        $classDir = ucfirst($classDir);

        // Get all the data definition models and search for a match
        foreach (glob(app_path().'/DataDefinitions/Models/'.$classDir.'/*.php') as $file) {
            $class = '\\App\\DataDefinitions\\Models\\'.$classDir.'\\'.basename($file, '.php');
            $dataDefinition = new $class;
            if ($dataDefinitionName === $dataDefinition->name) {
                return $dataDefinition;
            }
        }

        return null;
    }

    /**
     * Delete one or more records.
     *
     * @param  int|array  $ids ID or array with IDs of records to delete.
     * @param  string  $dataDefinitionName The name of the data definition to use for deleting records.
     * @return array The result message containing the type and text for the deletion process.
     *
     * @throws \Exception If the data definition is not found.
     */
    public function deleteRecords($ids, string $dataDefinitionName): array
    {
        // Find data definition
        $dataDefinition = $this->findDataDefinitionByName($dataDefinitionName);

        // If the data definition is found, create a new instance, otherwise throw an exception
        if ($dataDefinition !== null) {
            $dataDefinition = new $dataDefinition;
        } else {
            throw new \Exception('Data definition "'.$dataDefinitionName.'" not found');
        }

        // Get settings for the data definition
        $settings = $dataDefinition->getSettings([]);

        // Obtain the user type from the route name (member, staff, partner, or admin) 
        // and verify if the Data Definition is permitted for that user type
        $guard = explode('.', request()->route()->getName())[0];
        if ($settings['guard'] !== $guard) {
            abort(405, 'View not allowed for '.$guard.', '.$settings['guard'].' required');
        }

        // Make sure delete is allowed for data definition
        if (! $settings['delete']) {
            abort('Delete Not Allowed', 405);
        }

        // Ensure $ids is an array
        if (! is_array($ids)) {
            $ids = [$ids];
        }

        // Primary key
        $primaryKey = $dataDefinition->model->getKeyName();

        // Check if the model has an 'is_undeletable' column
        $hasUndeletableColumn = $dataDefinition->model->schemaHasColumn('is_undeletable');
        $oneOrMoreRecordsUndeletable = false;
        $recordsDeleted = 0;

        // Delete records
        foreach ($ids as $id) {
            // Only access to record(s) created by current user
            if ($settings['userMustOwnRecords']) {
                $user_id = auth($settings['guard'])->user()->id;
                $result = $dataDefinition->model->where('created_by', $user_id)->where($primaryKey, $id)->first();
            } else {
                $result = $dataDefinition->model->find($id);
            }

            // If the model has 'is_undeletable' column and the record is marked as undeletable, skip it
            if ($result === null || ($hasUndeletableColumn && $result->is_undeletable)) {
                $oneOrMoreRecordsUndeletable = true;
            } else {
                // Otherwise, delete the record
                $result->delete();
                $recordsDeleted++;
            }
        }

        // Set the appropriate result message based on the deletion results
        if ($oneOrMoreRecordsUndeletable && $recordsDeleted == 0) {
            $message = [
                'type' => 'danger',
                'size' => 'lg',
                'text' => trans('common.one_or_more_records_not_deleted'),
            ];
        } elseif ($oneOrMoreRecordsUndeletable && $recordsDeleted > 0) {
            $message = [
                'type' => 'warning',
                'size' => 'lg',
                'text' => trans('common.number_some_records_deleted', ['number' => $recordsDeleted]),
            ];
        } else {
            $message = [
                'type' => 'success',
                'size' => 'lg',
                'text' => trans('common.number_records_deleted', ['number' => $recordsDeleted]),
            ];
        }

        return $message;
    }

    /**
     * Sanitize input data in the request.
     *
     * This method iterates over each column in the provided array, and if the column
     * is not marked as 'allowHtml', it purifies the corresponding data in the request.
     *
     * @param Request $request The HTTP request containing the data to be sanitized.
     * @param array $columns An array of column configurations.
     *
     * @return void
     */
    public function sanitizeInput(Request $request, array $columns): void
    {
        // Instantiate a new HTML Purifier
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', ''); // Do not allow any HTML tags
        $purifier = new HTMLPurifier($config);

        // Iterate over each column in the array
        foreach ($columns as $column) {
            // Check if the column allows HTML
            if (! $column['allowHtml']) {
                // Get the input data
                $inputData = $request->input($column['name']);

                // Check if the input data is an array
                if (is_array($inputData)) {
                    // If it's an array, iterate over it and sanitize each item
                    foreach ($inputData as $key => $value) {
                        $inputData[$key] = $purifier->purify($value);
                    }
                } else {
                    // If it's not an array, sanitize it as before
                    $inputData = $purifier->purify($inputData);
                }

                // Merge the sanitized data back into the request
                $request->merge([$column['name'] => $inputData]);
            }
        }
    }

    /**
     * Insert a record using the specified data definition.
     *
     * @param  Request  $request The incoming HTTP request.
     * @param  array  $form The form configuration.
     * @param  array  $settings Additional settings.
     * @return array|\Illuminate\Validation\Validator The result message or a MessageBag instance in case of validation errors.
     *
     * @throws \Exception If the data definition is not found.
     */
    public function insertRecord(Request $request, array $form, array $settings): array|\Illuminate\Validation\Validator
    {
        // Obtain the user type from the route name (member, staff, partner, or admin) 
        // and verify if the Data Definition is permitted for that user type
        $guard = explode('.', $request->route()->getName())[0];
        if ($settings['guard'] !== $guard) {
            abort(405, 'View not allowed for '.$guard.', '.$settings['guard'].' required');
        }

        // Sanitize input
        $this->sanitizeInput($request, $form['columns']);

        // Prepare validation rules and custom messages for each column
        $vars = ['id' => null];
        [$rules, $customAttributeNames, $customMessages] = $this->prepareValidationRulesAndMessages($form['columns'], $request, $vars);

        // Validate the input data using the prepared rules and custom messages
        $validator = Validator::make($request->all(), $rules, $customMessages);

        // Set custom attribute names for the input fields
        $validator->setAttributeNames($customAttributeNames);

        // If validation fails, return the validation errors
        if ($validator->fails()) {
            return $validator;
        }

        // Process the input data for each column and update the record accordingly
        $this->processInputData($request, $form);

        // Add created_by
        $prefix = Route::getCurrentRoute()->getPrefix();
        $guard = ($prefix == '/{locale}') ? 'member' : str_replace('{locale}/', '', $prefix);
        $form['data']->created_by = auth($guard)->user()->id;

        // Process relations pre-save model
        foreach ($form['relations'] as $relation) {
            if ($relation['type'] == 'belongsTo') {
                $form['data']->{$relation['column']}()->associate($request->get($relation['column']));
            }
        }

        DB::transaction(function () use ($form, $request) {
            // Save the new record
            $form['data']->save();

            // Process relations post-save model
            foreach ($form['relations'] as $relation) {
                if (in_array($relation['type'], ['belongsToMany'])) {
                    if ($request->get($relation['column']) != '') {
                        $form['data']->{$relation['column']}()->sync($request->get($relation['column']));
                    }
                }
            }
        });

        // Send user password email
        if ($request->send_user_password) {
            // If correct, the current model record is a user
            $user = $form['data'];
            if ($user->email && $request->password != '') {
                $password = $request->password;
                $user->notify(new Registration($user->email, $password, $settings['mailUserPasswordGuard']));
            }
        }

        // Set the result message
        $message = [
            'type' => 'success',
            'size' => 'lg',
            'text' => trans('common.record_inserted'),
        ];

        return $message;
    }

    /**
     * Update a record using the specified data definition.
     *
     * @param  int  $id The ID of the record to update.
     * @param  Request  $request The incoming HTTP request.
     * @param  array  $form The form configuration.
     * @param  array  $settings Additional settings.
     * @return array|\Illuminate\Validation\Validator The result message or a MessageBag instance in case of validation errors.
     *
     * @throws \Exception If the data definition is not found.
     */
    public function updateRecord(int $id, Request $request, array $form, array $settings): array|\Illuminate\Validation\Validator
    {
        // Obtain the user type from the route name (member, staff, partner, or admin) 
        // and verify if the Data Definition is permitted for that user type
        $guard = explode('.', $request->route()->getName())[0];
        if ($settings['guard'] !== $guard) {
            abort(405, 'View not allowed for '.$guard.', '.$settings['guard'].' required');
        }

        // Determine if the model has an 'is_uneditable' column
        $hasUneditableColumn = $form['data']->schemaHasColumn('is_uneditable');

        // Check if the record is marked as uneditable
        $recordIsUneditable = $hasUneditableColumn && $form['data']->is_uneditable;

        if (! $recordIsUneditable) {
            // Sanitize input
            $this->sanitizeInput($request, $form['columns']);

            // Prepare validation rules and custom messages for each column
            $vars = ['id' => $id];
            [$rules, $customAttributeNames, $customMessages] = $this->prepareValidationRulesAndMessages($form['columns'], $request, $vars);

            // Validate the input data using the prepared rules and custom messages
            $validator = Validator::make($request->all(), $rules, $customMessages);

            // Set custom attribute names for the input fields
            $validator->setAttributeNames($customAttributeNames);

            // Validate user password
            if ($settings['editRequiresPassword']) {
                $validator->after(function ($validator) use ($request, $guard) {
                    $password = $request->current_password_required_to_save_changes;
                
                    // Get the current user's password hash
                    $currentPasswordHash = auth($guard)->user()->password;
                
                    // Check if the provided password matches the stored password hash
                    if (!Hash::check($password, $currentPasswordHash)) {
                        // Add an error message to the password field
                        $validator->errors()->add('current_password_required_to_save_changes', trans('common.validation.current_password'));
                    }
                });
            }

            // If validation fails, return the validation errors
            if ($validator->fails()) {
                return $validator;
            }

            // Process the input data for each column and update the record accordingly
            $this->processInputData($request, $form);

            // Process relations
            foreach ($form['relations'] as $relation) {
                // Unset column to prevent save error
                unset($form['data']->{$relation['column']});
            }

            // Add updated_by
            $form['data']->updated_by = auth($guard)->user()->id;

            DB::transaction(function () use ($form, $request) {
                // Save the updated record
                $form['data']->save();
    
                // Process relations
                foreach ($form['relations'] as $relation) {
                    // Save relation
                    if ($relation['type'] == 'belongsToMany') {
                        $relationIds = $request->get($relation['column']);

                        // If relationIds is empty, then sync with empty array to remove all existing relations
                        if (empty($relationIds)) {
                            $relationIds = [];
                        }

                        $form['data']->{$relation['column']}()->sync($relationIds);
                    }
                }
            });

            // Send user password email
            if ($request->send_user_password) {
                // If correct, the current model record is a user
                $user = $form['data'];
                if ($user->email && $request->password != '') {
                    $password = $request->password;
                    $user->notify(new Registration($user->email, $password, $settings['mailUserPasswordGuard']));
                }
            }
        }

        // Set the result message based on the record's editability status
        $message = $recordIsUneditable ? [
            'type' => 'danger',
            'size' => 'lg',
            'text' => trans('common.record_is_not_editable'),
        ] : [
            'type' => 'success',
            'size' => 'lg',
            'text' => trans('common.record_updated'),
        ];

        return $message;
    }

    /**
     * Prepare validation rules and custom messages for each column.
     *
     * @param  array  $columns The form configuration columns.
     * @param  Request  $request The incoming HTTP request.
     * @param  array  $vars Potential variables used in validation rules.
     * @return array An array containing the prepared validation rules and custom messages.
     */
    private function prepareValidationRulesAndMessages(array $columns, Request $request, array $vars): array
    {
        $rules = [];
        $customAttributeNames = [];
        $customMessages = [];

        // Iterate through each column in the form configuration
        foreach ($columns as $columnName => $column) {
            if ($column['translatable']) {
                foreach ($request->{$columnName} as $locale => $value) {
                    $rules[$columnName . '.' . $locale] = [];
                    $customAttributeNames[$columnName . '.' . $locale] = $columnName;
                }
            } else {
                $rules[$columnName] = [];
                $customAttributeNames[$columnName] = $column['text'];
            }

            // Process each validation rule for the current column
            foreach ($column['validate'] as $validate) {
                // Replace the ':id' placeholder with the actual ID if found
                if (strpos($validate, ':id') !== false) {
                    $validate = str_replace(':id', $vars['id'], $validate);
                }
                // Replace the ':option_keys' placeholder with the options array keys if found
                if (strpos($validate, ':option_keys') !== false && is_array($column['options'])) {
                    $option_keys = implode(',', array_keys($column['options']));
                    $validate = str_replace(':option_keys', $option_keys, $validate);
                }

                // Add the updated validation rule to the rules array
                if ($column['translatable']) {
                    foreach ($request->{$columnName} as $locale => $value) {
                        $rules[$columnName . '.' . $locale][] = $validate;
                    }
                } else {
                    $rules[$columnName][] = $validate;
                }

                // Prepare custom messages for image-related validation rules
                if (in_array($column['type'], ['image', 'avatar'])) {
                    if (strpos($validate, 'image') !== false) {
                        $customMessages["{$columnName}.image"] = trans('common.validation.image');
                    }
                    if (preg_match('/mimes:(.+)/', $validate, $matches)) {
                        $customMessages["{$columnName}.mimes"] = trans('common.validation.image_type', ['types' => $matches[1]]);
                    }
                    if (preg_match('/max:(\d+)/', $validate, $matches)) {
                        $customMessages["{$columnName}.max"] = trans('common.validation.image_size');
                    }
                    if (strpos($validate, 'dimensions') !== false) {
                        preg_match('/max_width=(\d+)/', $validate, $maxWidthMatches);
                        preg_match('/max_height=(\d+)/', $validate, $maxHeightMatches);
                        preg_match('/min_width=(\d+)/', $validate, $minWidthMatches);
                        preg_match('/min_height=(\d+)/', $validate, $minHeightMatches);
                        $maxWidth = $maxWidthMatches[1] ?? null;
                        $maxHeight = $maxHeightMatches[1] ?? null;
                        $minWidth = $minWidthMatches[1] ?? null;
                        $minHeight = $minHeightMatches[1] ?? null;

                        $customMessages["{$columnName}.dimensions"] = trans('common.validation.image_dimensions', ['maxWidth' => $maxWidth, 'maxHeight' => $maxHeight, 'minWidth' => $minWidth, 'minHeight' => $minHeight]);
                    }
                }
            }
        }

        return [$rules, $customAttributeNames, $customMessages];
    }

    /**
     * Process the input data for each column and update the record accordingly.
     *
     * @param  Request  $request The incoming HTTP request.
     * @param  array  $form The form configuration.
     */
    private function processInputData(Request $request, array &$form): void
    {
        foreach ($form['columns'] as $columnName => $column) {
            $columnInput = $request->get($columnName);

            // Handle datetime type format
            if (in_array($column['format'], ['datetime-local', 'datetime'])) {
                if ($columnInput != '') {
                    $carbonDate = Carbon::parse($columnInput);
                    $columnInput = $carbonDate->format('Y-m-d H:i:s');
                }
            }

            // Handle string type column
            if ($column['type'] == 'string') {
                if ($column['translatable']) {
                    // Same
                    $form['data']->{$columnName} = $columnInput;
                    /*
                    foreach ($request->{$columnName} as $locale => $value) {
                        $form['data']->{$columnName . '.' . $locale} = $columnInput;
                    }*/
                } else {
                    $form['data']->{$columnName} = $columnInput;
                }
            }
            // Handle password type column
            elseif ($column['type'] == 'password') {
                if ($columnInput != '') {
                    $form['data']->{$columnName} = bcrypt($columnInput);
                }
            }
            // Handle boolean type column
            elseif ($column['type'] == 'boolean') {
                $form['data']->{$columnName} = ($columnInput == 1) ? 1 : 0;
            }
            // Handle image and avatar type columns
            elseif ($column['type'] == 'image' || $column['type'] == 'avatar') {
                $uploadImage = $request->get($columnName.'_changed');
                $deleteImage = $request->get($columnName.'_deleted');

                // Delete the existing image if requested
                if ($deleteImage) {
                    $form['data']->clearMediaCollection($columnName);
                }

                // Validate and upload a new image if requested
                if ($uploadImage) {
                    $form['data']->addMediaFromRequest($columnName)->toMediaCollection($columnName);
                }
            }
            // Handle default
            else {
                if ($column['exists_in_database']) {
                    $form['data']->{$columnName} = $columnInput;
                }
            }

            if ($column['json']) {
                $json = $form['data']->{$column['json']};
                $json[$columnName] = $form['data']->{$columnName};
                $form['data']->{$column['json']} = $json;
                unset($form['data']->{$columnName});
            }
        }
    }
}
