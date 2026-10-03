<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('status', [
                'pending',
                'confirmed',
                'picked_up',
                'washing',
                'ready',
                'out_for_delivery',
                'delivered',
                'cancelled',
            ])->default('pending');

            $table->string('pickup_address');
            $table->string('delivery_address');
            $table->dateTime('scheduled_pickup_at')->nullable();
            $table->dateTime('scheduled_delivery_at')->nullable();

            $table->decimal('total_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};