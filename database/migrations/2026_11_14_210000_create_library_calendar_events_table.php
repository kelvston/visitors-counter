<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_calendar_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type', 20);
            $table->dateTime('starts_at')->index();
            $table->dateTime('ends_at');
            $table->boolean('is_all_day')->default(false);
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_calendar_events');
    }
};
