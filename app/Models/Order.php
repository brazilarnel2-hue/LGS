<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'driver_id',
        'status',
        'delivery_fee',
        'pickup_option',
        'return_option',
        'pickup_address',
        'delivery_address',
        'scheduled_pickup_at',
        'scheduled_delivery_at',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'scheduled_pickup_at' => 'datetime',
        'scheduled_delivery_at' => 'datetime',
        'delivery_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    /**
     * Driver trip fee based on how the laundry gets to and from the shop.
     *   pickup: 'driver' (driver collects) | 'shop' (customer drops off)
     *   return: 'deliver' (driver delivers) | 'shop' (customer picks up)
     */
    public static function calculateTripFee(string $pickup, string $return): float
    {
        $driverPickup = $pickup === 'driver';
        $deliver = $return === 'deliver';

        if ($driverPickup && $deliver) {
            return (float) config('laundry.package_fee', 0);
        }
        if ($driverPickup) {
            return (float) config('laundry.pickup_fee', 0);
        }
        if ($deliver) {
            return (float) config('laundry.delivery_fee', 0);
        }

        return 0.0;
    }

    /**
     * Label for the fee row (Pickup Fee / Delivery Fee / Pickup & Delivery Fee).
     * Older bookings have no options saved, so they fall back to "Delivery Fee".
     */
    public function getFeeLabelAttribute(): string
    {
        if ($this->pickup_option === 'driver' && $this->return_option === 'deliver') {
            return 'Pickup & Delivery Fee';
        }
        if ($this->pickup_option === 'driver') {
            return 'Pickup Fee';
        }

        return 'Delivery Fee';
    }

    /**
     * Total = sum of items + trip fee.
     */
    public function recalculateTotal(): void
    {
        $itemsTotal = $this->items()->sum('subtotal');

        $this->update([
            'total_amount' => $itemsTotal + ($this->delivery_fee ?? 0),
        ]);
    }

    public function updateStatus(string $status, ?int $changedBy = null): void
    {
        $this->update(['status' => $status]);
        $this->statusHistories()->create([
            'status' => $status,
            'changed_by' => $changedBy,
        ]);
    }
}