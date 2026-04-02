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
        Schema::create('service_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('variant_name', 200);
            $table->string('sku', 50)->unique()->index();
            $table->decimal('price', 12, 0);
            $table->decimal('sale_price', 12, 0)->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->json('includes')->nullable();
            $table->integer('max_bookings_per_day')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_variants');
    }
};
