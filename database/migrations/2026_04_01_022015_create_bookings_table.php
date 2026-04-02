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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 20)->unique()->index();
            $table->foreignId('customer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('guest_name', 150)->nullable();
            $table->string('guest_phone', 20)->nullable();
            $table->string('guest_email', 255)->nullable();
            $table->foreignId('service_id')->constrained('services');
            $table->foreignId('variant_id')->nullable()->constrained('service_variants')->nullOnDelete();
            $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('booking_date')->index();
            $table->time('booking_time');
            $table->time('end_time')->nullable();
            $table->string('location', 500)->nullable();
            $table->decimal('total_amount', 12, 0);
            $table->decimal('deposit_amount', 12, 0)->default(0);
            $table->decimal('discount_amount', 12, 0)->default(0);
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('pending')->index();
            $table->enum('payment_status', ['unpaid', 'deposit_paid', 'paid', 'refunded'])->default('unpaid')->index();
            $table->string('payment_method', 50)->nullable();
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('source', 50)->default('website')->index()->comment('website|zalo|phone|facebook|tiktok');
            $table->boolean('reminder_sent')->default(false);
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason', 500)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
