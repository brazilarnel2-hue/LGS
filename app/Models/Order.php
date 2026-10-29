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
        'barangay_id',
        'status',
        'delivery_fee',
        'pickup_option',
        'return_option',
        'delivery_zone',
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

    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
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

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Pricing formula: Base Fee + (Rate per KM x distance ng barangay mula sa shop).
     * Pag pickup AT delivery (package), may discount kumpara sa 2 hiwalay na trip,
     * kasi isang biyahe lang naman ng driver. I-adjust lang dito ang mga numbers
     * kung gusto mong baguhin ang presyo; ang distance ng bawat barangay ay
     * nasa `barangays` table.
     *
     * Note: hindi na dinadagdagan ng extra ang delivery fee base sa load — ang
     * "load" (8kg = ₱180) ay para lang sa presyo ng Wash & Fold na service
     * mismo, hindi sa delivery fee.
     */
    public const BASE_FEE = 20;
    public const RATE_PER_KM = 5;
    public const PACKAGE_MULTIPLIER = 1.5;

    /**
     * 1 load = KG_PER_LOAD kilo (base sa totoong practice ng shop: 8kg = ₱180
     * sa Wash & Fold price). Ginagamit lang ito para sa presyo ng service,
     * hindi para sa delivery fee.
     */
    public const KG_PER_LOAD = 8;

    /**
     * Bilang ng load (rounded up) base sa total na kilo ng mga "kg"-unit na
     * services sa isang booking (hal. Wash & Fold, Wash Only). Kung walang
     * kg-based na service (dry clean/ironing lang), 0 loads — walang dagdag.
     */
    public static function loadsForKg(float $totalKg): int
    {
        if ($totalKg <= 0) {
            return 0;
        }

        return (int) ceil($totalKg / self::KG_PER_LOAD);
    }

    /**
     * Fee for a single trip (either pickup OR delivery) going the given distance.
     */
    public static function singleTripFee(float $distanceKm): float
    {
        return self::BASE_FEE + (self::RATE_PER_KM * $distanceKm);
    }

    /**
     * Driver trip fee based on how the laundry gets to and from the shop,
     * at sa distansya ng barangay ng customer mula sa shop.
     *   pickup: 'driver' (driver collects) | 'shop' (customer drops off)
     *   return: 'deliver' (driver delivers) | 'shop' (customer picks up)
     *   distanceKm: Barangay->distance_km
     */
    public static function calculateTripFee(string $pickup, string $return, ?float $distanceKm = null): float
    {
        $driverPickup = $pickup === 'driver';
        $deliver = $return === 'deliver';

        if (!$driverPickup && !$deliver) {
            return 0.0;
        }

        if ($distanceKm === null) {
            return 0.0;
        }

        $single = self::singleTripFee($distanceKm);

        if ($driverPickup && $deliver) {
            return round($single * self::PACKAGE_MULTIPLIER, 2);
        }

        return round($single, 2);
    }

    /**
     * Tipid ng customer kapag package (pickup + delivery) kumpara sa 2 hiwalay na trip.
     * Ito yung "You save ₱xx" sa form.
     */
    public static function packageSavings(float $distanceKm): float
    {
        $single = self::singleTripFee($distanceKm);

        return round(($single * 2) - ($single * self::PACKAGE_MULTIPLIER), 2);
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