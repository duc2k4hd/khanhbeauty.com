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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploader_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('file_name', 300);
            $table->string('file_path', 500);
            $table->string('file_url', 500);
            $table->string('disk', 50)->default('local');
            $table->string('mime_type', 100);
            $table->integer('file_size_bytes')->unsigned();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('alt_text', 300)->nullable();
            $table->string('title', 300)->nullable();
            $table->text('caption')->nullable();
            $table->string('folder', 200)->nullable();
            $table->json('thumbnails')->nullable();
            $table->boolean('is_optimized')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
