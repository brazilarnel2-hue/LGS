<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangays', function (Blueprint $table) {
            // false = alam natin ang lugar pero hindi pa namin kayang puntahan
            // (pickup/delivery). Pwede pa rin silang mag-book kung sila mismo
            // ang maghahatid/kukuha sa shop.
            $table->boolean('is_serviceable')->default(true)->after('distance_km');
        });

        // Dagdag na entry para sa mga customer na wala sa listahan ng barangay
        // (hal. galing sa ibang bayan). Lalabas ito sa dulo ng dropdown.
        DB::table('barangays')->insert([
            'name' => 'Outside Abuyog (not in the list)',
            'distance_km' => 0,
            'is_serviceable' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('barangays')->where('name', 'Outside Abuyog (not in the list)')->delete();

        Schema::table('barangays', function (Blueprint $table) {
            $table->dropColumn('is_serviceable');
        });
    }
};