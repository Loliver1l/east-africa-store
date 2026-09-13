<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->string('slug')
                ->unique();

            $table->longText('description')
                ->nullable();

            $table->decimal('price_usd', 12, 2);

            $table->unsignedInteger('stock')
                ->default(0);

            $table->enum('status', [
                'available',
                'coming_soon',
                'out_of_stock',
                'draft',
            ])->default('draft');

            $table->timestamp('published_at')
                ->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
