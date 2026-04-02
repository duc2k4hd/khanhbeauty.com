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
        Schema::table('services', function (Blueprint $table) {
            $table->string('featured_image', 500)->nullable()->change();
        });

        Schema::table('portfolios', function (Blueprint $table) {
            $table->string('after_image', 500)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('featured_image', 500)->nullable(false)->change();
        });

        Schema::table('portfolios', function (Blueprint $table) {
            $table->string('after_image', 500)->nullable(false)->change();
        });
    }
};
