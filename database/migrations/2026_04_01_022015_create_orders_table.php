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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 20)->unique()->index();
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('guest_name', 150)->nullable();
            $table->string('guest_phone', 20)->nullable();
            $table->string('guest_email', 255)->nullable();
            $table->text('shipping_address');
            $table->string('shipping_province', 100)->nullable();
            $table->string('shipping_district', 100)->nullable();
            $table->string('shipping_ward', 100)->nullable();
            $table->decimal('subtotal', 12, 0);
            $table->decimal('shipping_fee', 12, 0)->default(0);
            $table->decimal('discount_amount', 12, 0)->default(0);
            $table->decimal('total_amount', 12, 0);
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->enum('status', ['pending', 'confirmed', 'shipping', 'delivered', 'completed', 'cancelled', 'returned'])->default('pending')->index();
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid')->index();
            $table->string('payment_method', 50)->nullable();
            $table->string('shipping_method', 100)->nullable();
            $table->string('tracking_code', 100)->nullable();
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('source', 50)->default('website')->index();
            $table->timestamp('completed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
