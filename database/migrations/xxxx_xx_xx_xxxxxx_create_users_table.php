<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->string('email')
                ->unique();

            $table->string('phone', 30)
                ->nullable()
                ->unique();

            $table->string('whatsapp_number', 30)
                ->nullable();

            $table->enum('role', [
                'customer',
                'admin',
            ])->default('customer');

            $table->string('referral_code')
                ->unique();

            $table->timestamp('email_verified_at')
                ->nullable();

            $table->string('password');

            $table->rememberToken();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
