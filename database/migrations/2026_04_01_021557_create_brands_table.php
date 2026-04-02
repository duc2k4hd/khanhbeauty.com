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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('slug', 191)->unique()->index();
            $table->string('logo_url', 500)->nullable();
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('brands');
    }
};
