<?php

namespace App\DataDefinitions\Models\Staff;

use App\DataDefinitions\DataDefinition;
use Illuminate\Database\Eloquent\Model;

class MemberDataDefinition extends DataDefinition
{
    /**
     * Unique for data definitions, url-friendly name for CRUD purposes.
     *
     * @var string
     */
    public $name = 'members';

    /**
     * The model associated with the definition.
     *
     * @var Model
     */
    public $model;

    /**
     * Settings.
     *
     * @var array
     */
    public $settings;

    /**
     * Model fields for list, edit, view.
     *
     * @var array
     */
    public $fields;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Set the model
        $this->model = new \App\Models\Member;

        // Define the fields for the data definition
        $this->fields = [
            'avatar' => [
                'thumbnail' => 'small', // Image conversion used for list
                'conversion' => 'medium', // Image conversion used for view/edit
                'text' => trans('common.avatar'),
                'type' => 'avatar',
                'textualAvatarBasedOnColumn' => 'name',
                'accept' => 'image/svg+xml, image/png, image/jpeg, image/gif',
                'validate' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:1024', 'dimensions:min_width=60,min_height=60,max_width=1024,max_height=1024'],
                'classes::list' => 'md-only:hidden',
                'actions' => ['list', 'insert', 'edit', 'view'],
            ],
            'name' => [
                'text' => trans('common.name'),
                'type' => 'string',
                'searchable' => true,
                'sortable' => true,
                'validate' => ['required', 'max:120'],
                'actions' => ['list', 'insert', 'edit', 'view'],
            ],
            'email' => [
                'text' => trans('common.email_address'),
                'type' => 'string',
                'format' => 'hideEmail',
                'searchable' => true,
                'sortable' => true,
                'validate' => ['required', 'email', 'max:120', 'unique:admins,email,:id'],
                'actions' => ['list', 'insert', 'edit', 'view'],
            ],
            'linked_cards' => [
                'text' => trans('common.loyalty_cards'),
                'filter' => false,
                'type' => 'manyToMany',
                'relationThrough' => 'transactions',
                'relationThroughPivot' => 'card',
                'relationThroughValue' => 'head',
                'relationThroughOrderByColumn' => 'transactions.created_at',
                'relationThroughOrderDirection' => 'desc',
                'relationThrough' => 'transactions',
                'relationThroughModel' => new \App\Models\Transaction,
                'relationThroughFilter' => function ($query) {
                    return $query->whereHas('card', function ($query) {
                        $query->where('transactions.created_at', '>=', \Carbon\Carbon::now()->subDays(config('default.staff_transaction_days_ago')));
                    });
                },
                'relationThroughLink' => function ($row, $column, $transaction) {
                    return route('staff.transactions', [
                        'member_identifier' => $row->unique_identifier,
                        'card_identifier' => $transaction->{$column['relationThroughPivot']}->unique_identifier
                    ]);
                },                
                'actions' => ['list'],
            ],
            'created_at' => [
                'text' => trans('common.created'),
                'type' => 'date_time',
                'actions' => ['view'],
            ],
            'created_by' => [
                'text' => trans('common.created_by'),
                'type' => 'user.admin',
                'actions' => ['view'],
            ],
            'unique_identifier' => [
                'hidden' => true,
                'type' => 'dummy',
                'actions' => ['list'],
            ],
        ];

        // Define the general settings for the data definition
        $this->settings = [
            // Query filter
            'queryFilter' => function ($query) {
                $staffId = auth('staff')->user()->id;
                return $query->where('is_active', true)->whereHas('transactions', function ($query) use ($staffId) {
                    $query->where('transactions.staff_id', $staffId)
                          ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(config('default.staff_transaction_days_ago')));
                });
            },
            // Icon
            'icon' => 'user-group',
            // Title (plural of subject)
            'title' => trans('common.members'),
            // Override title (this title is shown in every view, without sub title like "Edit item" or "View item")
            'overrideTitle' => null,
            // Guard of user that manages this data (member, staff, partner or admin)
            // This guard is also used for routes and to include the correct Blade layout
            'guard' => 'staff',
            // Used for updating forms like profile, where user has to enter current password in order to save
            'editRequiresPassword' => false,
            // If true, the visitor is redirected to the edit form
            'redirectListToEdit' => false,
            // This column has to match auth($guard)->user()->id if 'redirectListToEdit' == true (usually it will be 'id' or 'created_by')
            'redirectListToEditColumn' => null,
            // If true, the user id must match the created_by field
            'userMustOwnRecords' => false,
            // Should there be checkboxes for all rows
            'multiSelect' => false,
            // Default items per page for pagination
            'itemsPerPage' => 10,
            // Order by column
            'orderByColumn' => 'id',
            // Order direction, 'asc' or 'desc'
            'orderDirection' => 'desc',
            // Order by relation
            'orderRelation' => function($query) {
                $query->from('transactions')
                    ->select('created_at')
                    ->whereColumn('member_id', 'members.id')
                    ->orderBy('created_at', 'desc')
                    ->limit(1);
            },
            // Possible actions for the data
            'actions' => [
                'subject_column' => 'name', // This column is used for page titles and delete confirmations
                'list' => true,
                'insert' => false,
                'edit' => false,
                'delete' => false,
                'view' => false,
                'export' => false,
            ],
        ];
    }

    /**
     * Do not modify below this line.
     *
     * ---------------------------------
     */

    /**
     * Retrieve data based on fields.
     *
     * @param  string  $dateDefinitionName
     * @param  Model  $model
     */
    public function getData(string $dateDefinitionName = null, string $dateDefinitionView = 'list', array $options = [], Model $model = null, array $settings = [], array $fields = []): array
    {
        return parent::getData($this->name, $dateDefinitionView, $options, $this->model, $this->settings, $this->fields);
    }

    /**
     * Parse settings.
     */
    public function getSettings(array $settings): array
    {
        return parent::getSettings($this->settings);
    }
}
