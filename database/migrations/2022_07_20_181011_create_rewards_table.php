<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to create the 'rewards' table.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rewards', function (Blueprint $table) {
            // Primary key and foreign keys
            $table->id();

            // Reward details
            $table->string('name', 250);
            $table->json('title');
            $table->json('description')->nullable();
            $table->integer('max_number_to_redeem')->default(0);
            $table->integer('points')->unsigned();
            $table->timestamp('active_from')->nullable();;
            $table->timestamp('expiration_date')->nullable();;

            // Reward activation
            $table->boolean('is_active')->default(1);

            // Reward statistics
            $table->integer('number_of_times_redeemed')->default(0)->unsigned();
            $table->integer('views')->default(0)->unsigned();
            $table->timestamp('last_view')->nullable();
            $table->boolean('is_undeletable')->default(0);
            $table->boolean('is_uneditable')->default(0);

            // Meta information
            $table->json('meta')->nullable();

            // Ownership and timestamps
            $table->bigInteger('created_by')->nullable()->unsigned()->index();
            $table->foreign('created_by')->references('id')->on('partners')->onDelete('set null');
            $table->bigInteger('deleted_by')->nullable()->unsigned()->index();
            $table->foreign('deleted_by')->references('id')->on('partners')->onDelete('set null');
            $table->bigInteger('updated_by')->nullable()->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations to drop the 'rewards' table.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rewards');
    }
};
