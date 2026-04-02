<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'booking_code',
    'customer_id',
    'guest_name',
    'guest_phone',
    'guest_email',
    'service_id',
    'variant_id',
    'staff_id',
    'booking_date',
    'booking_time',
    'end_time',
    'location',
    'total_amount',
    'deposit_amount',
    'discount_amount',
    'status',
    'payment_status',
    'payment_method',
    'notes',
    'admin_notes',
    'source',
    'reminder_sent',
    'cancelled_at',
    'cancel_reason',
])]
class Booking extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'total_amount' => 'decimal:0',
            'deposit_amount' => 'decimal:0',
            'discount_amount' => 'decimal:0',
            'reminder_sent' => 'boolean',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * Get the customer (user) for the booking.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the staff (user) for the booking.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Get the service for the booking.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * Get the variant for the booking.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ServiceVariant::class, 'variant_id');
    }

    /**
     * Scope a query to only include bookings for a specific status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
