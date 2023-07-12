<?php

namespace App\DataDefinitions\Models\Partner;

use App\DataDefinitions\DataDefinition;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RewardDataDefinition extends DataDefinition
{
    /**
     * Unique for data definitions, url-friendly name for CRUD purposes.
     *
     * @var string
     */
    public $name = 'rewards';

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
        $this->model = new \App\Models\Reward;

        // Define the fields for the data definition
        $this->fields = [
            'tab1' => [
                'title' => trans('common.details'),
                'fields' => [
                    'name' => [
                        'text' => trans('common.name'),
                        'help' => trans('common.reward_name_text'),
                        'type' => 'string',
                        'highlight' => true,
                        'searchable' => true,
                        'sortable' => true,
                        'validate' => ['required', 'max:120'],
                        'actions' => ['list', 'insert', 'edit', 'view', 'export'],
                    ],
                    'points' => [
                        'text' => trans('common.points'),
                        'help' => trans('common.reward_points_text'),
                        'type' => 'string',
                        'format' => 'number',
                        'sortable' => true,
                        'default' => null,
                        'min' => 0,
                        'max' => 10000000,
                        'step' => 1,
                        'validate' => ['required', 'numeric', 'min:0', 'max:10000000'],
                        'actions' => ['list', 'insert', 'edit', 'view', 'export'],
                    ],
                    'active_from' => [
                        'text' => trans('common.active_from'),
                        'type' => 'string',
                        'format' => 'datetime-local',
                        'default' => Carbon::now(auth('partner')->user()->time_zone)->format('Y-m-d H:i'),
                        'validate' => ['required', 'date'],
                        'container_start::insert' => 'grid grid-cols-2 gap-4',
                        'container_start::edit' => 'grid grid-cols-2 gap-4',
                        'actions' => ['insert', 'edit', 'view', 'export'],
                    ],
                    'expiration_date' => [
                        'text' => trans('common.expiration_date'),
                        'type' => 'string',
                        'format' => 'datetime-local',
                        'sortable' => true,
                        'default' => Carbon::now(auth('partner')->user()->time_zone)->addYears(1)->format('Y-m-d H:i'),
                        'validate' => ['required', 'date'],
                        'container_end::insert' => true,
                        'container_end::edit' => true,
                        'actions' => ['list', 'insert', 'edit', 'view', 'export'],
                    ],
                    'is_active' => [
                        'text' => trans('common.active'),
                        'type' => 'boolean',
                        'validate' => ['nullable', 'boolean'],
                        'format' => 'icon',
                        'sortable' => true,
                        'default' => true,
                        'help' => trans('common.reward_is_active_text'),
                        'classes::list' => 'lg-only:hidden',
                        'actions' => ['list', 'insert', 'edit', 'view', 'export'],
                    ],
                ]
            ],
            'tab2' => [
                'title' => trans('common.content'),
                'fields' => [
                    'title' => [
                        'text' => trans('common.title'),
                        'type' => 'string',
                        'translatable' => true,
                        'searchable' => true,
                        'validate' => ['required', 'max:120'],
                        'actions' => ['insert', 'edit', 'view', 'export'],
                    ],
                    'description' => [
                        'text' => trans('common.description'),
                        'type' => 'textarea',
                        'translatable' => true,
                        'validate' => ['nullable', 'max:800'],
                        'actions' => ['insert', 'edit', 'view', 'export'],
                    ],
                ]
            ],
            'tab3' => [
                'title' => trans('common.images'),
                'fields' => [
                    'image1' => [
                        'thumbnail' => 'xs', // Image conversion used for list
                        'conversion' => 'md', // Image conversion used for view/edit
                        'text' => trans('common.main_image'),
                        'type' => 'image',
                        'accept' => 'image/png, image/jpeg, image/gif',
                        'validate' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048', 'dimensions:min_width=320,min_height=240,max_width=1920,max_height=1440'],
                        'classes::list' => 'md-only:hidden',
                        'actions' => ['list', 'insert', 'edit', 'view'],
                    ],
                    'image2' => [
                        'thumbnail' => 'xs', // Image conversion used for list
                        'conversion' => 'md', // Image conversion used for view/edit
                        'text' => trans('common.image_no', ['number' => 2]),
                        'type' => 'image',
                        'accept' => 'image/png, image/jpeg, image/gif',
                        'validate' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048', 'dimensions:min_width=320,min_height=240,max_width=1920,max_height=1440'],
                        'actions' => ['insert', 'edit', 'view'],
                    ],
                    'image3' => [
                        'thumbnail' => 'xs', // Image conversion used for list
                        'conversion' => 'md', // Image conversion used for view/edit
                        'text' => trans('common.image_no', ['number' => 3]),
                        'type' => 'image',
                        'accept' => 'image/png, image/jpeg, image/gif',
                        'validate' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048', 'dimensions:min_width=320,min_height=240,max_width=1920,max_height=1440'],
                        'actions' => ['insert', 'edit', 'view'],
                    ],
                ]
            ],
            'views' => [
                'text' => trans('common.views'),
                'type' => 'number',
                'sortable' => true,
                'actions' => ['list', 'view', 'export'],
            ],
            'created_at' => [
                'text' => trans('common.created'),
                'type' => 'date_time',
                'actions' => ['view', 'export'],
            ],
            'created_by' => [
                'text' => trans('common.created_by'),
                'type' => 'user.partner',
                'actions' => ['view', 'export'],
            ],
            'updated_at' => [
                'text' => trans('common.updated'),
                'type' => 'date_time',
                'actions' => ['view', 'export'],
            ],
            'updated_by' => [
                'text' => trans('common.updated_by'),
                'type' => 'user.partner',
                'actions' => ['view', 'export'],
            ],
        ];

        // Define the general settings for the data definition
        $this->settings = [
            // Icon
            'icon' => 'gift',
            // Title (plural of subject)
            'title' => trans('common.rewards'),
            // Override title (this title is shown in every view, without sub title like "Edit item" or "View item")
            'overrideTitle' => null,
            // Description
            'description' => trans('common.partner_rewards_description'),
            // Guard of user that manages this data (member, staff, partner or admin)
            // This guard is also used for routes and to include the correct Blade layout
            'guard' => 'partner',
            // If set, these role(s) are required
            'roles' => [1],
            // Used for updating forms like profile, where user has to enter current password in order to save
            'editRequiresPassword' => false,
            // If true, the visitor is redirected to the edit form
            'redirectListToEdit' => false,
            // This column has to match auth($guard)->user()->id if 'redirectListToEdit' == true (usually it will be 'id' or 'created_by')
            'redirectListToEditColumn' => null,
            // If true, the user id must match the created_by field
            'userMustOwnRecords' => true,
            // Should there be checkboxes for all rows
            'multiSelect' => true,
            // Default items per page for pagination
            'itemsPerPage' => 20,
            // Order by column
            'orderByColumn' => 'points',
            // Order direction, 'asc' or 'desc'
            'orderDirection' => 'asc',
            // Possible actions for the data
            'actions' => [
                'subject_column' => 'name', // This column is used for page titles and delete confirmations
                'list' => true,
                'insert' => true,
                'edit' => true,
                'delete' => true,
                'view' => true,
                'export' => true,
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
