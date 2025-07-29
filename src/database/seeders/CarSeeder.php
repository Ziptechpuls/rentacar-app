<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\CarModel;
use App\Models\CarImage;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    public function run(): void
    {
        $carModels = CarModel::all();

        // ダミー画像URL（外部サービスを使用）
        $dummyImages = [
            'https://picsum.photos/400/300?random=1',
            'https://picsum.photos/400/300?random=2',
            'https://picsum.photos/400/300?random=3',
            'https://picsum.photos/400/300?random=4',
            'https://picsum.photos/400/300?random=5',
        ];

        foreach ($carModels as $carModel) {
            // 各車種に対して3台ずつ作成
            for ($i = 1; $i <= 3; $i++) {
                $car = Car::create([
                    'car_model_id' => $carModel->id,
                    'name' => $carModel->name . ' ' . $i,
                    'type' => match ($carModel->grade) {
                        'コンパクト' => 'コンパクト',
                        'ミドル' => 'セダン',
                        'ラグジュアリー' => 'ミニバン',
                        default => 'セダン',
                    },
                    'capacity' => match ($carModel->grade) {
                        'コンパクト' => 5,
                        'ミドル' => 5,
                        'ラグジュアリー' => 8,
                        default => 5,
                    },
                    'price' => match ($carModel->grade) {
                        'コンパクト' => 5000,
                        'ミドル' => 8000,
                        'ラグジュアリー' => 15000,
                        default => 8000,
                    },
                    'transmission' => 'AT',
                    'smoking_preference' => 'non-smoking',
                    'has_bluetooth' => true,
                    'has_back_monitor' => true,
                    'has_navigation' => true,
                    'has_etc' => true,
                    'description' => $carModel->description,
                    'is_public' => true,
                ]);

                // ダミー画像をランダムに追加（1-3枚）
                $imageCount = rand(1, 3);
                for ($j = 0; $j < $imageCount; $j++) {
                    $imageUrl = $dummyImages[array_rand($dummyImages)];
                    
                    CarImage::create([
                        'car_id' => $car->id,
                        'image_path' => $imageUrl,
                    ]);
                }
            }
        }
    }
}
