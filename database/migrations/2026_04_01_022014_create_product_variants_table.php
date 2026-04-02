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
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('sku', 80)->unique()->index();
            $table->string('variant_name', 300);
            $table->decimal('price', 12, 0);
            $table->decimal('sale_price', 12, 0)->nullable();
            $table->decimal('cost_price', 12, 0)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->string('image_url', 500)->nullable();
            $table->string('barcode', 50)->nullable();
            $table->integer('weight_grams')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->softDeletes();
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
