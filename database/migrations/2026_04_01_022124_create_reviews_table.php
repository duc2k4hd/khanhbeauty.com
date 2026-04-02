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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->morphs('reviewable');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('guest_name', 150)->nullable();
            $table->tinyInteger('rating')->unsigned();
            $table->string('title', 200)->nullable();
            $table->text('content')->nullable();
            $table->json('images')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_approved')->default(false)->index();
            $table->text('admin_reply')->nullable();
            $table->timestamp('admin_reply_at')->nullable();
            $table->integer('helpful_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
