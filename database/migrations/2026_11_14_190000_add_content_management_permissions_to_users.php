<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_manage_academic_minutes')->default(false);
            $table->boolean('can_manage_administrative_minutes')->default(false);
            $table->boolean('can_manage_proposals')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'can_manage_academic_minutes',
                'can_manage_administrative_minutes',
                'can_manage_proposals',
            ]);
        });
    }
};
