<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sender_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('receiver_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('subject')
                ->nullable();

            $table->longText('message');

            $table->enum('status', [
                'unread',
                'read',
                'archived',
            ])->default('unread');

            $table->timestamp('read_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'receiver_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
