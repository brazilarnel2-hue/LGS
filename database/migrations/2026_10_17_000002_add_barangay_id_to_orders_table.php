<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Nullable: ang mga lumang order at ang mga order na sa shop lang
            // (walang driver) ay walang barangay.
            $table->unsignedBigInteger('barangay_id')->nullable()->index()->after('driver_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['barangay_id']);
            $table->dropColumn('barangay_id');
        });
    }
};