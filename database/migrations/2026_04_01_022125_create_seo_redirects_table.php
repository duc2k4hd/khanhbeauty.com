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
        Schema::create('seo_redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from_url', 250)->unique()->index();
            $table->string('to_url', 500);
            $table->integer('status_code')->default(301)->comment('301|302');
            $table->integer('hit_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_redirects');
    }
};
