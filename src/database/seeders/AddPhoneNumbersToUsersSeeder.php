<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Faker\Factory as Faker;

class AddPhoneNumbersToUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('ja_JP');
        
        // 電話番号が未設定のユーザーを取得
        $usersWithoutPhone = User::whereNull('phone_number')->get();
        
        foreach ($usersWithoutPhone as $user) {
            // 日本の電話番号形式でランダム生成
            $phoneNumber = $faker->phoneNumber();
            
            // より自然な日本の電話番号形式に調整
            $phoneNumber = preg_replace('/[^\d]/', '', $phoneNumber);
            if (strlen($phoneNumber) >= 10) {
                $phoneNumber = substr($phoneNumber, 0, 10);
                $phoneNumber = '0' . $phoneNumber;
            }
            
            // ハイフンで区切る
            if (strlen($phoneNumber) === 11) {
                $phoneNumber = substr($phoneNumber, 0, 3) . '-' . substr($phoneNumber, 3, 4) . '-' . substr($phoneNumber, 7);
            }
            
            $user->update(['phone_number' => $phoneNumber]);
        }
        
        $this->command->info('電話番号を追加しました: ' . $usersWithoutPhone->count() . ' ユーザー');
    }
}
