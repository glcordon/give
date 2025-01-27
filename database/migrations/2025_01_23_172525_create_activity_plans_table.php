<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityPlansTable extends Migration
{
    public function up()
    {
        Schema::create('activity_plans', function (Blueprint $table) {
            $table->id();
            $table->string('event_name');
            $table->text('description')->nullable();
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('location');
            $table->json('rooms')->nullable();
            $table->json('resources')->nullable();
            $table->decimal('budget', 10, 2)->nullable();
            $table->text('special_notes')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('member_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_plans');
    }
}
