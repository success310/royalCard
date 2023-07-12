<?php

  /*
   |--------------------------------------------------------------------------
   | Defaults
   |--------------------------------------------------------------------------
   |
   | The values below are defaults for the app.
   | Can be overridden in the .env file.
   |
   */

  return [
      /*
      |--------------------------------------------------------------------------
      | Force SSL
      |--------------------------------------------------------------------------
      */

      'force_ssl' => env('FORCE_SSL', false),

      /*
      |--------------------------------------------------------------------------
      | SEO
      |--------------------------------------------------------------------------
      */

      'page_title_delimiter' => env('PAGE_TITLE_DELIMITER', ' - '),

      /*
       |--------------------------------------------------------------------------
       | App
       |--------------------------------------------------------------------------
       */

      'app_name' => env('APP_NAME', 'Reward Loyalty'),
      'app_logo' => env('APP_LOGO', ''),
      'app_logo_dark' => env('APP_LOGO_DARK', ''),
      'app_url' => env('APP_URL', 'https://localhost'),
      'app_is_installed' => env('APP_IS_INSTALLED', false),
      'app_admin_email' => env('APP_ADMIN_EMAIL', env('MAIL_FROM_ADDRESS', 'admin@example.com')),
      'app_demo' => env('APP_DEMO', false),

      /*
       |--------------------------------------------------------------------------
       | E-mail
       |--------------------------------------------------------------------------
       */

      'mail_from_name' => env('MAIL_FROM_NAME', 'Reward Loyalty'),
      'mail_from_address' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),

      /*
       |--------------------------------------------------------------------------
       | Localization
       |--------------------------------------------------------------------------
       */

      'time_zone' => env('DEFAULT_TIMEZONE', 'America/Los_Angeles'),
      'currency' => env('DEFAULT_CURRENCY', 'USD'),

      /*
       |--------------------------------------------------------------------------
       | Number of days that a staff member can see a member he/she interacted with
       |--------------------------------------------------------------------------
       */

      'staff_transaction_days_ago' => env('APP_STAFF_TRANSACTION_DAYS_AGO', 7),
  ];
