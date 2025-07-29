<?php

namespace Database\Seeders;

use App\Models\CarModel;
use Illuminate\Database\Seeder;

class CarModelSeeder extends Seeder
{
    public function run(): void
    {
        $carModels = [
            ['name' => 'フィット', 'grade' => 'コンパクト', 'description' => 'コンパクトで使いやすい車種です。', 'manufacturer' => 'ホンダ'],
            ['name' => 'ノート', 'grade' => 'コンパクト', 'description' => '燃費が良く、街乗りに最適です。', 'manufacturer' => '日産'],
            ['name' => 'アクア', 'grade' => 'コンパクト', 'description' => 'ハイブリッドで環境にやさしい車種です。', 'manufacturer' => 'トヨタ'],
            ['name' => 'プリウス', 'grade' => 'ミドル', 'description' => '快適な乗り心地と優れた燃費を両立しています。', 'manufacturer' => 'トヨタ'],
            ['name' => 'カローラ', 'grade' => 'ミドル', 'description' => '安定性と信頼性に優れた定番車種です。', 'manufacturer' => 'トヨタ'],
            ['name' => 'クラウン', 'grade' => 'ラグジュアリー', 'description' => '高級感と快適性を追求した車種です。', 'manufacturer' => 'トヨタ'],
            ['name' => 'アルファード', 'grade' => 'ラグジュアリー', 'description' => '広々とした室内で、長距離ドライブも快適です。', 'manufacturer' => 'トヨタ'],
        ];

        foreach ($carModels as $carModel) {
            CarModel::create($carModel);
        }
    }
}

