<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // テスト用管理者アカウント作成
        \App\Models\Admin::create([
            'name' => 'テスト管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        // 各種シーダー実行
        $this->call([
            CarModelSeeder::class,
            CarSeeder::class,
            OptionSeeder::class,
        ]);
    }
}
