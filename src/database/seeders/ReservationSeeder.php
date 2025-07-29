<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Option;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        // 過去3ヶ月分の予約を作成
        $cars = Car::all();
        $users = User::all();
        $options = Option::all();

        foreach ($cars as $car) {
            // 各車両に対して10件の予約を作成
            for ($i = 0; $i < 10; $i++) {
                // ランダムな日時を生成（過去3ヶ月以内）
                $startDate = Carbon::now()->subMonths(3)->addDays(rand(0, 90));
                $endDate = (clone $startDate)->addDays(rand(1, 7));

                // 基本料金を計算（日数 × 車両の1日料金）
                $days = $startDate->diffInDays($endDate) + 1;
                $basePrice = $car->price * $days;

                // 予約を作成
                $reservation = Reservation::create([
                    'user_id' => $users->random()->id,
                    'car_id' => $car->id,
                    'name_kanji' => '山田 太郎',
                    'name_kana_sei' => 'ヤマダ',
                    'name_kana_mei' => 'タロウ',
                    'phone_main' => '090-1234-5678',
                    'phone_emergency' => '090-8765-4321',
                    'email' => 'yamada@example.com',
                    'start_datetime' => $startDate,
                    'end_datetime' => $endDate,
                    'status' => rand(1, 10) <= 8 ? 'confirmed' : (rand(0, 1) ? 'pending' : 'cancelled'), // 80%が確定、残りは保留かキャンセル
                    'total_price' => $basePrice, // 一旦基本料金をセット、後でオプション料金を追加
                ]);

                // ランダムにオプションを追加（0-3個）
                $numOptions = rand(0, 3);
                $optionTotal = 0;
                
                if ($numOptions > 0) {
                    $selectedOptions = $options->random($numOptions);
                    foreach ($selectedOptions as $option) {
                        $quantity = $option->is_quantity ? rand(1, 3) : 1;
                        $optionPrice = $option->price * $quantity * $days;
                        $optionTotal += $optionPrice;
                        
                        $reservation->options()->attach($option->id, [
                            'quantity' => $quantity,
                            'price' => $option->price,
                            'total_price' => $optionPrice,
                        ]);
                    }
                }

                // 合計金額を更新
                $reservation->update([
                    'total_price' => $basePrice + $optionTotal,
                ]);
            }
        }
    }
}