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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('product_categories')->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->string('name', 300);
            $table->string('slug', 191)->unique()->index();
            $table->string('sku', 50)->unique()->index();
            $table->string('short_description', 500)->nullable();
            $table->longText('description');
            $table->decimal('price', 12, 0);
            $table->decimal('sale_price', 12, 0)->nullable();
            $table->decimal('cost_price', 12, 0)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->integer('low_stock_threshold')->default(5);
            $table->integer('weight_grams')->nullable();
            $table->string('featured_image', 500);
            $table->json('gallery')->nullable();
            $table->string('video_url', 500)->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_digital')->default(false);
            $table->integer('sort_order')->default(0);
            $table->integer('view_count')->default(0);
            $table->integer('sold_count')->default(0);
            $table->decimal('avg_rating', 2, 1)->default(0);
            $table->integer('review_count')->default(0);
            $table->string('meta_title', 160)->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->json('schema_markup')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
