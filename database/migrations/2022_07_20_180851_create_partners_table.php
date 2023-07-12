<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to create the 'partners' table.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partners', function (Blueprint $table) {
            // Primary key and foreign key
            $table->id();
            $table->bigInteger('network_id')->nullable()->unsigned()->index();
            $table->foreign('network_id')->references('id')->on('networks')->onDelete('CASCADE');

            // Account details
            $table->tinyInteger('role')->nullable()->default(1); // 1 = regular partner
            $table->string('display_name', 64)->nullable(); // Visible to other users
            $table->string('name', 128)->nullable();
            $table->string('email', 128)->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();

            // Two-Factor Authentication
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('two_factor_secret')->nullable();
            $table->string('two_factor_recovery_codes')->nullable();

            // Account expiration
            $table->timestamp('account_expires_at')->nullable();
            $table->timestamp('premium_expires_at')->nullable();

            // Localization settings
            $table->string('locale', 12)->nullable();
            $table->char('country_code', 2)->nullable();
            $table->char('currency', 3)->nullable();
            $table->string('time_zone', 48)->nullable();

            // Phone information
            $table->string('phone_prefix', 4)->nullable();
            $table->string('phone_country', 2)->nullable();
            $table->string('phone', 24)->nullable();
            $table->string('phone_e164', 24)->nullable();

            // Account settings
            $table->boolean('is_active')->default(1);
            $table->boolean('is_undeletable')->default(0);
            $table->boolean('is_uneditable')->default(0);

            // Login statistics
            $table->integer('number_of_times_logged_in')->unsigned()->default(0);
            $table->timestamp('last_login_at')->nullable();

            // Meta information
            $table->json('meta')->nullable();

            // Ownership and timestamps
            $table->bigInteger('created_by')->nullable()->unsigned()->index();
            $table->foreign('created_by')->references('id')->on('admins')->onDelete('CASCADE');
            $table->bigInteger('deleted_by')->nullable()->unsigned()->index();
            $table->foreign('deleted_by')->references('id')->on('admins')->onDelete('CASCADE');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations to drop the 'partners' table.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partners');
    }
};
