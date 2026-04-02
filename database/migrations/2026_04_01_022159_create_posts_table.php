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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 300);
            $table->string('slug', 191)->unique()->index();
            $table->string('excerpt', 500)->nullable();
            $table->longText('content');
            $table->string('featured_image', 500)->nullable();
            $table->string('featured_image_alt', 200)->nullable();
            $table->enum('status', ['draft', 'published', 'scheduled', 'archived'])->default('draft')->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('reading_time_min')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('allow_comments')->default(true);
            $table->string('meta_title', 160)->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->string('meta_keywords', 500)->nullable();
            $table->string('og_title', 200)->nullable();
            $table->string('og_description', 300)->nullable();
            $table->string('og_image', 500)->nullable();
            $table->string('canonical_url', 500)->nullable();
            $table->string('focus_keyword', 100)->nullable();
            $table->json('secondary_keywords')->nullable();
            $table->integer('internal_links_count')->default(0);
            $table->integer('external_links_count')->default(0);
            $table->integer('word_count')->default(0);
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
        Schema::dropIfExists('posts');
    }
};
