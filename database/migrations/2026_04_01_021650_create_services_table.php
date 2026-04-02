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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('service_categories')->cascadeOnDelete();
            $table->string('name', 250);
            $table->string('slug', 191)->unique()->index();
            $table->string('short_description', 500);
            $table->longText('description');
            $table->decimal('price', 12, 0);
            $table->decimal('sale_price', 12, 0)->nullable();
            $table->string('price_unit', 50)->default('buổi');
            $table->integer('duration_minutes')->nullable();
            $table->string('featured_image', 500);
            $table->json('gallery')->nullable();
            $table->json('includes')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0);
            $table->integer('view_count')->default(0);
            $table->integer('booking_count')->default(0);
            $table->decimal('avg_rating', 2, 1)->default(0);
            $table->string('meta_title', 160)->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->json('schema_markup')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
