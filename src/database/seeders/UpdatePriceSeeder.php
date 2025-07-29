<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CarPrice;

class UpdatePriceSeeder extends Seeder
{
    public function run(): void
    {
        // 既存の料金データを取得
        $carPrices = CarPrice::all();

        foreach ($carPrices as $price) {
            // 既存の6時間料金から3時間料金を計算（80%）
            $price_3h = (int)($price->price_6h * 0.8);
            
            // 既存の12時間料金から営業時間内料金を計算（85%）
            $price_business = (int)($price->price_12h * 0.85);

            // 新しい料金を設定
            $price->update([
                'price_3h' => $price_3h,
                'price_business' => $price_business,
            ]);
        }
    }
} 