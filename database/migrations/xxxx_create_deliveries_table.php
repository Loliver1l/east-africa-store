<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('notification_method', [
                'email',
                'whatsapp',
                'both',
            ]);

            $table->string('email')
                ->nullable();

            $table->string('whatsapp_number', 30)
                ->nullable();

            $table->text('address');

            $table->string('city')
                ->nullable();

            $table->string('county')
                ->nullable();

            $table->string('country')
                ->default('Kenya');

            $table->enum('status', [
                'pending',
                'processing',
                'shipped',
                'delivered',
                'cancelled',
            ])->default('pending');

            $table->string('tracking_number')
                ->nullable();

            $table->timestamp('shipped_at')
                ->nullable();

            $table->timestamp('delivered_at')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
