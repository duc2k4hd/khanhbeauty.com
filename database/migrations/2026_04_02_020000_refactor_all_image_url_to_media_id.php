<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Refactor: Thay đổi tất cả bảng lưu URL ảnh sang lưu media_id (FK → media table).
 * Các bảng bị reset: services, products, posts, pages, portfolios,
 * service_categories, product_categories, post_categories,
 * users (avatar), product_variants, product_attribute_values, reviews.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. SERVICES ──────────────────────────────────────
        Schema::table('services', function (Blueprint $table) {
            $table->unsignedBigInteger('featured_image_id')->nullable()->after('duration_minutes');
            $table->json('gallery_ids')->nullable()->after('featured_image_id');
            $table->foreign('featured_image_id')->references('id')->on('media')->nullOnDelete();
        });
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['featured_image', 'gallery']);
        });

        // ── 2. PRODUCTS ───────────────────────────────────────
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('featured_image_id')->nullable()->after('weight_grams');
            $table->json('gallery_ids')->nullable()->after('featured_image_id');
            $table->foreign('featured_image_id')->references('id')->on('media')->nullOnDelete();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['featured_image', 'gallery']);
        });

        // ── 3. POSTS ──────────────────────────────────────────
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('featured_image_id')->nullable()->after('excerpt');
            $table->unsignedBigInteger('og_image_id')->nullable()->after('featured_image_id');
            $table->foreign('featured_image_id')->references('id')->on('media')->nullOnDelete();
            $table->foreign('og_image_id')->references('id')->on('media')->nullOnDelete();
        });
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['featured_image', 'featured_image_alt', 'og_image']);
        });

        // ── 4. PAGES ──────────────────────────────────────────
        Schema::table('pages', function (Blueprint $table) {
            $table->unsignedBigInteger('featured_image_id')->nullable()->after('content');
            $table->unsignedBigInteger('og_image_id')->nullable()->after('featured_image_id');
            $table->foreign('featured_image_id')->references('id')->on('media')->nullOnDelete();
            $table->foreign('og_image_id')->references('id')->on('media')->nullOnDelete();
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['featured_image', 'og_image']);
        });

        // ── 5. PORTFOLIOS ─────────────────────────────────────
        Schema::table('portfolios', function (Blueprint $table) {
            $table->unsignedBigInteger('before_image_id')->nullable()->after('category');
            $table->unsignedBigInteger('after_image_id')->nullable()->after('before_image_id');
            $table->json('gallery_ids')->nullable()->after('after_image_id');
            $table->foreign('before_image_id')->references('id')->on('media')->nullOnDelete();
            $table->foreign('after_image_id')->references('id')->on('media')->nullOnDelete();
        });
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropColumn(['before_image', 'after_image', 'gallery']);
        });

        // ── 6. SERVICE_CATEGORIES ─────────────────────────────
        Schema::table('service_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('image_id')->nullable()->after('icon');
            $table->foreign('image_id')->references('id')->on('media')->nullOnDelete();
        });
        Schema::table('service_categories', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });

        // ── 7. PRODUCT_CATEGORIES ─────────────────────────────
        Schema::table('product_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('image_id')->nullable()->after('description');
            $table->foreign('image_id')->references('id')->on('media')->nullOnDelete();
        });
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });

        // ── 8. POST_CATEGORIES ────────────────────────────────
        Schema::table('post_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('image_id')->nullable()->after('description');
            $table->foreign('image_id')->references('id')->on('media')->nullOnDelete();
        });
        Schema::table('post_categories', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });

        // ── 9. USERS (avatar) ─────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('avatar_id')->nullable()->after('password');
            $table->foreign('avatar_id')->references('id')->on('media')->nullOnDelete();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar_url');
        });

        // ── 10. PRODUCT_VARIANTS ──────────────────────────────
        Schema::table('product_variants', function (Blueprint $table) {
            $table->unsignedBigInteger('image_id')->nullable()->after('sku');
            $table->foreign('image_id')->references('id')->on('media')->nullOnDelete();
        });
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });

        // ── 11. PRODUCT_ATTRIBUTE_VALUES ──────────────────────
        Schema::table('product_attribute_values', function (Blueprint $table) {
            $table->unsignedBigInteger('image_id')->nullable()->after('color_code');
            $table->foreign('image_id')->references('id')->on('media')->nullOnDelete();
        });
        Schema::table('product_attribute_values', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });

        // ── 12. REVIEWS (images: JSON array of media IDs) ─────
        // Giữ nguyên tên cột 'images', chỉ đổi semantics: mảng URL → mảng media IDs
        // Không cần đổi schema, chỉ đổi logic ở code
        // (đã là JSON, giờ lưu int IDs thay vì URL strings)
    }

    public function down(): void
    {
        // Revert services
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['featured_image_id']);
            $table->dropColumn(['featured_image_id', 'gallery_ids']);
            $table->string('featured_image', 500)->nullable();
            $table->json('gallery')->nullable();
        });

        // Revert products
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['featured_image_id']);
            $table->dropColumn(['featured_image_id', 'gallery_ids']);
            $table->string('featured_image', 500)->nullable();
            $table->json('gallery')->nullable();
        });

        // Revert posts
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['featured_image_id', 'og_image_id']);
            $table->dropColumn(['featured_image_id', 'og_image_id']);
            $table->string('featured_image', 500)->nullable();
            $table->string('featured_image_alt', 200)->nullable();
            $table->string('og_image', 500)->nullable();
        });

        // Revert pages
        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeign(['featured_image_id', 'og_image_id']);
            $table->dropColumn(['featured_image_id', 'og_image_id']);
            $table->string('featured_image', 500)->nullable();
            $table->string('og_image', 500)->nullable();
        });

        // Revert portfolios
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropForeign(['before_image_id', 'after_image_id']);
            $table->dropColumn(['before_image_id', 'after_image_id', 'gallery_ids']);
            $table->string('before_image', 500)->nullable();
            $table->string('after_image', 500);
            $table->json('gallery')->nullable();
        });

        // Revert categories
        Schema::table('service_categories', function (Blueprint $table) {
            $table->dropForeign(['image_id']);
            $table->dropColumn('image_id');
            $table->string('image_url', 500)->nullable();
        });
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropForeign(['image_id']);
            $table->dropColumn('image_id');
            $table->string('image_url', 500)->nullable();
        });
        Schema::table('post_categories', function (Blueprint $table) {
            $table->dropForeign(['image_id']);
            $table->dropColumn('image_id');
            $table->string('image_url', 500)->nullable();
        });

        // Revert users
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['avatar_id']);
            $table->dropColumn('avatar_id');
            $table->string('avatar_url', 500)->nullable();
        });

        // Revert product_variants
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropForeign(['image_id']);
            $table->dropColumn('image_id');
            $table->string('image_url', 500)->nullable();
        });

        // Revert product_attribute_values
        Schema::table('product_attribute_values', function (Blueprint $table) {
            $table->dropForeign(['image_id']);
            $table->dropColumn('image_id');
            $table->string('image_url', 500)->nullable();
        });
    }
};
