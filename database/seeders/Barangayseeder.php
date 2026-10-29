<?php

namespace Database\Seeders;

use App\Models\Barangay;
use Illuminate\Database\Seeder;

class BarangaySeeder extends Seeder
{
    /**
     * Kumpletong listahan ng 63 barangay ng Abuyog, Leyte.
     *
     * Ang distance_km dito ay ESTIMATE lang (base sa pangkalahatang
     * lokasyon ng bawat barangay — malapit sa bayan, katamtaman, o
     * malayo/bundok) — hindi 100% exact Google Maps measurement tulad
     * ng mga barangay na na-type mo na mismo dati gamit ang totoong link.
     *
     * firstOrCreate = kung EXISTING na yung barangay (halimbawa yung
     * mga na-type mo na dati gamit ang totoong Google Maps distance,
     * tulad ng Guintagbucan at Can-uguib), HINDI ito ma-o-overwrite —
     * mananatili ang totoong distance mo doon. Dadagdagan lang nito
     * yung mga barangay na wala pa sa listahan.
     */
    public function run(): void
    {
        $barangays = [
            // --- Malapit sa bayan / shop area (estimate: 0.5–5 km) ---
            'Bunga' => 0.5, // dito mismo nakatirik ang shop
            'Bito' => 2.0,
            'Santo Niño' => 2.0,
            'San Roque' => 2.5,
            'Bulak' => 2.5,
            'Nalibunan' => 2.5,
            'Libertad' => 3.0,
            'Buntay' => 3.0,
            'San Isidro' => 3.0,
            'Dingle' => 3.5,
            'San Francisco' => 3.5,
            'Santa Fe' => 3.5,
            'Katipunan' => 4.0,
            'Picas Sur' => 4.0,
            'Santa Lucia' => 4.0,
            'Loyonsawang' => 4.5,
            'Pilar' => 4.5,
            'Victory' => 4.5,

            // --- Katamtamang layo (estimate: 6–12 km) ---
            'Odiongan' => 6.0,
            'Alangilan' => 6.0,
            'Anibongan' => 6.5,
            'Bayabas' => 6.5,
            'Kikilo' => 6.5,
            'Bagacay' => 7.0,
            'Cagbolo' => 7.0,
            'Tabigue' => 7.0,
            'Bahay' => 7.5,
            'Capilian' => 7.5,
            'Paguite' => 7.5,
            'Barayong' => 8.0,
            'New Taligue' => 8.0,
            'Tadoc' => 8.0,
            'Can-uguib' => 8.5,
            'Old Taligue' => 8.5,
            'Tinalian' => 8.5,
            'Buenavista' => 8.5,
            'Burubud-an' => 9.0,
            'Combis' => 9.0,
            'Pinamanagan' => 9.0,
            'Can-aporong' => 9.5,
            'Guintagbucan' => 9.5,
            'Tinocolan' => 9.5,
            'Cadac-an' => 10.0,
            'Laray' => 10.0,
            'Salvacion' => 10.0,
            'Canmarating' => 10.5,
            'Pagsang-an' => 10.5,
            'Hampipila' => 11.0,
            'Tuy-a' => 11.0,
            'Parasanon' => 11.5,
            'Tib-o' => 12.0,

            // --- Malayo / bundok (estimate: 13–23 km) ---
            'Buaya' => 13.0,
            'Balinsasayao' => 14.0,
            'Nebga' => 14.5,
            'Balocawe' => 15.0,
            'Mag-atubang' => 15.5,
            'Balocawehay' => 16.0,
            'Matagnao' => 16.5,
            'Lawa-an' => 17.0,
            'Mahagna' => 18.0,
            'Mahayahay' => 19.0,
            'Maitum' => 21.0,
            'Malaguicay' => 23.0,
        ];

        foreach ($barangays as $name => $distanceKm) {
            Barangay::firstOrCreate(
                ['name' => $name],
                ['distance_km' => $distanceKm, 'is_serviceable' => true]
            );
        }

        // Siguraduhing meron pa rin ang "Outside Abuyog" fallback option.
        Barangay::firstOrCreate(
            ['name' => 'Outside Abuyog (not in the list)'],
            ['distance_km' => 0, 'is_serviceable' => false]
        );
    }
}