<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('video_url')->nullable()->after('meta_title');
        });

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'video_url')) {
                $table->string('video_url')->nullable()->after('meta_title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('video_url');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('video_url');
        });
    }
};
