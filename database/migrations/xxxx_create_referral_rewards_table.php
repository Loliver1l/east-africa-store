<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_rewards', function (Blueprint $table) {
            $table->id();

            $table->foreignId('referral_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('order_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->decimal('percentage', 5, 2);

            $table->decimal('amount_usd', 12, 2);

            $table->enum('status', [
                'pending',
                'approved',
                'paid',
                'cancelled',
            ])->default('pending');

            $table->timestamp('paid_at')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'referral_id',
                'order_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_rewards');
    }
};
