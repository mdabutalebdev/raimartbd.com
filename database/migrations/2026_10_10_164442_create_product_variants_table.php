<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            
            $table->json('attributes')->nullable(); // e.g. {"Size": "XL", "Color": "Red"}
            $table->json('dimensions')->nullable();
            $table->json('images')->nullable();
            
            $table->string('sku')->nullable();
            $table->integer('stock')->default(0);
            
            $table->decimal('current_price', 10, 2)->default(0);
            $table->decimal('original_price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            
            $table->decimal('weight', 10, 2)->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->dateTime('available_from')->nullable();
            $table->dateTime('available_until')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
