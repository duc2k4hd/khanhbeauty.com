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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title', 300);
            $table->string('slug', 191)->unique()->index();
            $table->longText('content');
            $table->string('template', 100)->default('default');
            $table->string('featured_image', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('meta_title', 160)->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->string('og_image', 500)->nullable();
            $table->json('schema_markup')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
