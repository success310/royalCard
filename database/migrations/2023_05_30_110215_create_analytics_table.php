<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('analytics', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys and indexes for participants and relations
            $table->bigInteger('partner_id')->unsigned()->index();
            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('CASCADE');
            $table->bigInteger('member_id')->nullable()->unsigned()->index();
            $table->foreign('member_id')->references('id')->on('members')->onDelete('set null');
            $table->bigInteger('staff_id')->nullable()->unsigned()->index();
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('set null');
            $table->bigInteger('card_id')->nullable()->unsigned()->index();
            $table->foreign('card_id')->references('id')->on('cards')->onDelete('CASCADE');
            $table->bigInteger('reward_id')->nullable()->unsigned()->index();
            $table->foreign('reward_id')->references('id')->on('rewards')->onDelete('set null');

            // Stat details
            $table->string('event', 250)->nullable();
            $table->string('locale', 12)->nullable();
            $table->char('currency', 3)->nullable();
            $table->bigInteger('purchase_amount')->unsigned()->nullable();
            $table->integer('points')->nullable();

            // Meta information
            $table->json('meta')->nullable();

            // Ownership and timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics');
    }
};
