<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Laravelアプリケーションを起動
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "既存のユーザーに電話番号を追加します...\n";

// 電話番号が未設定のユーザーを取得
$usersWithoutPhone = User::whereNull('phone_number')->get();

echo "電話番号未設定のユーザー数: " . $usersWithoutPhone->count() . "\n";

foreach ($usersWithoutPhone as $user) {
    // ランダムな日本の電話番号を生成
    $areaCode = ['03', '06', '052', '044', '045', '075', '078', '092', '011', '0120'][array_rand(['03', '06', '052', '044', '045', '075', '078', '092', '011', '0120'])];
    $number1 = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    $number2 = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    $phoneNumber = $areaCode . '-' . $number1 . '-' . $number2;
    
    $user->update(['phone_number' => $phoneNumber]);
    
    echo "ユーザー {$user->name} に電話番号 {$phoneNumber} を追加しました\n";
}

echo "完了しました！\n"; 