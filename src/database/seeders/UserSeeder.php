<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // テストユーザーを作成
        User::create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'phone_number' => '090-1234-5678',
        ]);

        // ダミーユーザーを20件作成
        $dummyUsers = [
            ['name' => '佐藤 一郎', 'email' => 'sato.ichiro@example.com'],
            ['name' => '鈴木 次郎', 'email' => 'suzuki.jiro@example.com'],
            ['name' => '高橋 三郎', 'email' => 'takahashi.saburo@example.com'],
            ['name' => '田中 四郎', 'email' => 'tanaka.shiro@example.com'],
            ['name' => '伊藤 五郎', 'email' => 'ito.goro@example.com'],
            ['name' => '渡辺 花子', 'email' => 'watanabe.hanako@example.com'],
            ['name' => '山本 明子', 'email' => 'yamamoto.akiko@example.com'],
            ['name' => '中村 裕子', 'email' => 'nakamura.yuko@example.com'],
            ['name' => '小林 和子', 'email' => 'kobayashi.kazuko@example.com'],
            ['name' => '加藤 真理', 'email' => 'kato.mari@example.com'],
            ['name' => '吉田 健一', 'email' => 'yoshida.kenichi@example.com'],
            ['name' => '山田 太郎', 'email' => 'yamada.taro@example.com'],
            ['name' => '佐々木 次郎', 'email' => 'sasaki.jiro@example.com'],
            ['name' => '山口 三郎', 'email' => 'yamaguchi.saburo@example.com'],
            ['name' => '松本 四郎', 'email' => 'matsumoto.shiro@example.com'],
            ['name' => '井上 五郎', 'email' => 'inoue.goro@example.com'],
            ['name' => '木村 花子', 'email' => 'kimura.hanako@example.com'],
            ['name' => '林 明子', 'email' => 'hayashi.akiko@example.com'],
            ['name' => '斎藤 裕子', 'email' => 'saito.yuko@example.com'],
            ['name' => '清水 和子', 'email' => 'shimizu.kazuko@example.com'],
        ];

        foreach ($dummyUsers as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('password'),
                'phone_number' => '090-' . rand(1000, 9999) . '-' . rand(1000, 9999),
            ]);
        }
    }
}