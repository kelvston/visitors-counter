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
        Schema::table('counters', function (Blueprint $table) {
            $table->softDeletes(); // This adds a deleted_at column for soft deletes
            $table->string('deletion_reason')->nullable(); // This adds a deletion reason column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('counters', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('deletion_reason');
        });
    }
};
