<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to create the 'cards' table.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            // Primary key and foreign key
            $table->id();
            $table->bigInteger('club_id')->nullable()->unsigned()->index();
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('CASCADE');

            // Card content and details
            $table->string('name', 250);
            $table->string('type', 32)->default('loyalty');
            $table->string('icon', 32)->nullable();
            $table->json('head')->nullable();
            $table->json('title')->nullable();
            $table->json('description')->nullable();
            $table->string('unique_identifier', 32)->nullable()->index(); // Unique number in format of: xxx-xxx-xxx-xxx
            $table->timestamp('issue_date')->useCurrent();
            $table->timestamp('expiration_date')->nullable();

            // Card design
            $table->string('bg_color', 25)->nullable();
            $table->tinyInteger('bg_color_opacity')->nullable();
            $table->string('text_color', 32)->nullable();
            $table->string('text_label_color', 32)->nullable();
            $table->string('qr_color_light', 32)->nullable();
            $table->string('qr_color_dark', 32)->nullable();

            // Card features and settings
            $table->char('currency', 3)->nullable();
            $table->integer('initial_bonus_points')->unsigned()->nullable();
            $table->integer('points_expiration_months')->unsigned()->nullable();
            $table->integer('currency_unit_amount')->unsigned()->nullable();
            $table->integer('points_per_currency')->unsigned()->nullable();
            $table->decimal('point_value', 8, 4)->unsigned()->nullable();
            $table->bigInteger('min_points_per_purchase')->unsigned()->nullable();
            $table->bigInteger('max_points_per_purchase')->unsigned()->nullable();
            $table->bigInteger('min_points_per_redemption')->unsigned()->nullable();
            $table->bigInteger('max_points_per_redemption')->unsigned()->nullable();
            $table->json('custom_rule1')->nullable();
            $table->json('custom_rule2')->nullable();
            $table->json('custom_rule3')->nullable();

            // Card activation and visibility
            $table->boolean('is_active')->default(1);
            $table->boolean('is_visible_by_default')->default(0);
            $table->boolean('is_visible_when_logged_in')->default(0);
            $table->boolean('is_undeletable')->default(0);
            $table->boolean('is_uneditable')->default(0);

            // Card statistics
            $table->integer('total_amount_purchased')->unsigned()->default(0);
            $table->integer('number_of_points_issued')->unsigned()->default(0);
            $table->timestamp('last_points_issued_at')->nullable();
            $table->integer('number_of_points_redeemed')->unsigned()->default(0);
            $table->integer('number_of_rewards_redeemed')->unsigned()->default(0);
            $table->timestamp('last_reward_redeemed_at')->nullable();
            $table->integer('views')->default(0)->unsigned();
            $table->timestamp('last_view')->nullable();

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
     * Reverse the migrations to drop the 'cards' table.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cards');
    }
};
