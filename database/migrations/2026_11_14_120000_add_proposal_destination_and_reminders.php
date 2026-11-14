<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->string('destination', 2048)->nullable();
        });

        Schema::create('proposal_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('days_before');
            $table->timestamp('sent_at');
            $table->unique(['proposal_id', 'user_id', 'days_before']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_reminders');

        Schema::table('proposals', function (Blueprint $table) {
            $table->dropColumn('destination');
        });
    }
};
