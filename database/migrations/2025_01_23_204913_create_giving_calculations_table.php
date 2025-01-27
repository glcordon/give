<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGivingCalculationsTable extends Migration
{
    public function up()
    {
        Schema::create('giving_calculations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->date('date')->nullable();
            $table->foreignId('activity_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->json('checks')->nullable();      // JSON for counters
            $table->json('counters')->nullable();      // JSON for counters
            $table->json('other_donations')->nullable(); // JSON for other donations
            $table->unsignedInteger('denomination_1')->default(0); // Total cash
            $table->unsignedInteger('denomination_5')->default(0); // Total cash
            $table->unsignedInteger('denomination_10')->default(0); // Total cash
            $table->unsignedInteger('denomination_20')->default(0); // Total cash
            $table->unsignedInteger('denomination_50')->default(0); // Total cash
            $table->unsignedInteger('denomination_100')->default(0); // Total cash
            $table->unsignedInteger('denomination_penny')->default(0); // Total cash
            $table->unsignedInteger('denomination_nickel')->default(0); // Total cash
            $table->unsignedInteger('denomination_dime')->default(0); // Total cash
            $table->unsignedInteger('denomination_quarter')->default(0); // Total cash
            $table->unsignedInteger('denomination_half_dollar')->default(0); // Total cash
            $table->unsignedInteger('denomination_coin_dollar')->default(0); // Total cash
            $table->decimal('total_cash', 10, 2)->default(0); // Total cash
            $table->decimal('total_coin', 10, 2)->default(0); // Total coin
            $table->decimal('total_cash_coin', 10, 2)->default(0); // Total cash + coin
            $table->decimal('total_checks', 10, 2)->default(0); // Total checks
            $table->decimal('total_giving', 10, 2)->default(0); // Total giving
            $table->decimal('total_bank_deposit', 10, 2)->default(0); // Total bank deposit
            $table->timestamps();
            $table->softDeletes();
            Schema::enableForeignKeyConstraints();
        });

       
    }

    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('giving_calculations');
    }
}
