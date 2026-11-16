<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mail_conversations', function (Blueprint $table) {
            $table->foreignId('sender_id')->nullable()->change();
        });

        Schema::table('mail_messages', function (Blueprint $table) {
            $table->foreignId('sender_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('mail_conversations')->whereNull('sender_id')->delete();

        Schema::table('mail_messages', function (Blueprint $table) {
            $table->foreignId('sender_id')->nullable(false)->change();
        });

        Schema::table('mail_conversations', function (Blueprint $table) {
            $table->foreignId('sender_id')->nullable(false)->change();
        });
    }
};
