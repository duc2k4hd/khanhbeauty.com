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
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->string('title', 300);
            $table->string('slug', 191)->unique()->index();
            $table->text('description')->nullable();
            $table->string('category', 100)->nullable()->comment('bride|event|daily|nail|photo');
            $table->string('before_image', 500)->nullable();
            $table->string('after_image', 500);
            $table->json('gallery')->nullable();
            $table->string('client_name', 150)->nullable();
            $table->json('services_used')->nullable()->comment('service ids');
            $table->json('products_used')->nullable()->comment('product ids');
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->string('meta_title', 160)->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
