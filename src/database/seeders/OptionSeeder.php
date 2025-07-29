<?php

namespace Database\Seeders;

use App\Models\Option;
use Illuminate\Database\Seeder;

class OptionSeeder extends Seeder
{
    public function run(): void
    {
        $options = [
            [
                'name' => 'チャイルドシート',
                'description' => '6歳未満のお子様用のチャイルドシートです。',
                'price' => 1000,
                'is_quantity' => true,
                'price_type' => 'per_piece',
            ],
            [
                'name' => 'ジュニアシート',
                'description' => '6歳以上のお子様用のジュニアシートです。',
                'price' => 800,
                'is_quantity' => true,
                'price_type' => 'per_piece',
            ],
            [
                'name' => 'スキーキャリア',
                'description' => 'スキー・スノーボードを運搬できるキャリアです。',
                'price' => 2000,
                'is_quantity' => false,
                'price_type' => 'per_day',
            ],
            [
                'name' => 'サーフボードキャリア',
                'description' => 'サーフボードを運搬できるキャリアです。',
                'price' => 2000,
                'is_quantity' => false,
                'price_type' => 'per_day',
            ],
            [
                'name' => '4G Wi-Fiルーター',
                'description' => '車内で使用できる4G Wi-Fiルーターです。',
                'price' => 500,
                'is_quantity' => false,
                'price_type' => 'per_day',
            ],
            [
                'name' => 'ドライブレコーダー',
                'description' => '前後カメラ搭載の高性能ドライブレコーダーです。',
                'price' => 1000,
                'is_quantity' => false,
                'price_type' => 'per_day',
            ],
        ];

        foreach ($options as $option) {
            Option::create($option);
        }
    }
}