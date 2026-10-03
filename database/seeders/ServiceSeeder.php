<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Wash & Fold',
                'description' => 'Regular wash, dry, and fold service.',
                'price' => 60.00,
                'unit' => 'kg',
            ],
            [
                'name' => 'Dry Clean',
                'description' => 'For delicate fabrics and formal wear.',
                'price' => 150.00,
                'unit' => 'item',
            ],
            [
                'name' => 'Ironing',
                'description' => 'Pressing service for wrinkle-free clothes.',
                'price' => 20.00,
                'unit' => 'item',
            ],
            [
                'name' => 'Wash Only',
                'description' => 'Washing without drying or folding.',
                'price' => 40.00,
                'unit' => 'kg',
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}