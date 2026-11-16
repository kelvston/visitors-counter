<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_message_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mail_message_id')->constrained('mail_messages')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 3);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->unique(['mail_message_id', 'user_id']);
            $table->index(['user_id', 'read_at']);
        });

        DB::table('mail_messages')
            ->whereNotNull('recipient_id')
            ->orderBy('id')
            ->chunkById(500, function ($messages): void {
                foreach ($messages as $message) {
                    DB::table('mail_message_recipients')->insert([
                        'mail_message_id' => $message->id,
                        'user_id' => $message->recipient_id,
                        'type' => 'to',
                        'read_at' => $message->read_at,
                        'created_at' => $message->created_at,
                        'updated_at' => $message->updated_at,
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_message_recipients');
    }
};
