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

    public function recalculateTotal(): void
    {
        $this->update(['total_amount' => $this->items()->sum('subtotal')]);
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