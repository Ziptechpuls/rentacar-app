<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CarTypePrice;

class CarTypePriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carTypePrices = [
            [
                'car_type' => '軽自動車',
                'price_3h' => 2000,
                'price_business' => 3000,
                'price_24h' => 5000,
                'price_48h' => 9000,
                'price_72h' => 13000,
                'price_168h' => 25000,
                'price_extra_hour' => 300,
                'is_active' => true,
            ],
            [
                'car_type' => 'コンパクト',
                'price_3h' => 2500,
                'price_business' => 3500,
                'price_24h' => 6000,
                'price_48h' => 11000,
                'price_72h' => 16000,
                'price_168h' => 30000,
                'price_extra_hour' => 400,
                'is_active' => true,
            ],
            [
                'car_type' => 'セダン',
                'price_3h' => 3000,
                'price_business' => 4000,
                'price_24h' => 7000,
                'price_48h' => 13000,
                'price_72h' => 19000,
                'price_168h' => 35000,
                'price_extra_hour' => 500,
                'is_active' => true,
            ],
            [
                'car_type' => 'SUV',
                'price_3h' => 3500,
                'price_business' => 4500,
                'price_24h' => 8000,
                'price_48h' => 15000,
                'price_72h' => 22000,
                'price_168h' => 40000,
                'price_extra_hour' => 600,
                'is_active' => true,
            ],
            [
                'car_type' => 'ミニバン',
                'price_3h' => 4000,
                'price_business' => 5000,
                'price_24h' => 9000,
                'price_48h' => 17000,
                'price_72h' => 25000,
                'price_168h' => 45000,
                'price_extra_hour' => 700,
                'is_active' => true,
            ],
            [
                'car_type' => 'ステーションワゴン',
                'price_3h' => 3200,
                'price_business' => 4200,
                'price_24h' => 7500,
                'price_48h' => 14000,
                'price_72h' => 20500,
                'price_168h' => 38000,
                'price_extra_hour' => 550,
                'is_active' => true,
            ],
            [
                'car_type' => 'その他',
                'price_3h' => 2800,
                'price_business' => 3800,
                'price_24h' => 6500,
                'price_48h' => 12000,
                'price_72h' => 17500,
                'price_168h' => 32000,
                'price_extra_hour' => 450,
                'is_active' => true,
            ],
        ];

        foreach ($carTypePrices as $carTypePrice) {
            CarTypePrice::updateOrCreate(
                ['car_type' => $carTypePrice['car_type']],
                $carTypePrice
            );
        }
    }
}
