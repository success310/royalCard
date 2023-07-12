<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to create the 'clubs' table.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clubs', function (Blueprint $table) {
            // Primary key and foreign key
            $table->id();

            // Club details
            $table->string('name', 96)->nullable();
            $table->text('description')->nullable();
            $table->string('host', 250)->nullable();
            $table->string('slug', 250)->nullable();

            // Address and location
            $table->string('location', 128)->nullable();
            $table->string('street1', 250)->nullable();
            $table->string('street2', 250)->nullable();
            $table->string('box_number', 32)->nullable();
            $table->string('postal_code', 32)->nullable();
            $table->string('city', 128)->nullable();
            $table->string('admin1', 164)->nullable();
            $table->string('admin2', 164)->nullable();
            $table->integer('geoname_id')->unsigned()->nullable();
            $table->string('region', 64)->nullable();
            $table->integer('region_geoname_id')->unsigned()->nullable();
            $table->char('country_code', 2)->nullable();
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('lng', 11, 8)->nullable();

            // Localization settings
            $table->string('locale', 12)->nullable();
            $table->char('currency', 3)->nullable();
            $table->string('time_zone', 48)->nullable();

            // Club settings
            $table->boolean('is_active')->default(1);
            $table->boolean('is_primary')->default(0);
            $table->boolean('is_undeletable')->default(0);
            $table->boolean('is_uneditable')->default(0);

            // Meta information
            $table->json('meta')->nullable();

            // Ownership and timestamps
            $table->bigInteger('created_by')->nullable()->unsigned()->index();
            $table->foreign('created_by')->references('id')->on('partners')->onDelete('CASCADE');
            $table->bigInteger('deleted_by')->nullable()->unsigned()->index();
            $table->foreign('deleted_by')->references('id')->on('partners')->onDelete('CASCADE');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations to drop the 'clubs' table.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clubs');
    }
};
