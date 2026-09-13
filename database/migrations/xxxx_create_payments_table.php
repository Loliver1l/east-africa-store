<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('provider', [
                'paystack',
                'binance',
            ]);

            $table->enum('payment_method', [
                'paystack_mpesa',
                'paystack_card',
                'binance_crypto',
            ]);

            $table->string('currency', 10)
                ->default('USD');

            $table->decimal('amount_usd', 12, 2);

            $table->decimal('amount_crypto', 24, 8)
                ->nullable();

            $table->string('crypto_currency', 20)
                ->nullable();

            $table->string('crypto_network', 50)
                ->nullable();

            $table->string('provider_order_id')
                ->nullable();

            $table->string('provider_transaction_id')
                ->nullable();

            $table->string('provider_reference')
                ->nullable();

            $table->string('idempotency_key')
                ->nullable()
                ->unique();

            $table->enum('status', [
                'pending',
                'processing',
                'paid',
                'failed',
                'cancelled',
                'refunded',
            ])->default('pending');

            $table->json('provider_response')
                ->nullable();

            $table->timestamp('paid_at')
                ->nullable();

            $table->timestamp('failed_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'provider',
                'status',
            ]);

            $table->index('provider_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
