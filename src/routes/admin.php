<?php

use App\Http\Controllers\Admin\OptionController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Platform\Auth\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Platform Admin Routes
|--------------------------------------------------------------------------
|
| admin.localhost 専用のルート定義
|
*/

// ログイン前
Route::middleware('guest:platform')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('platform.login');
    Route::post('/login', [LoginController::class, 'login']);
});

// ログイン後
Route::middleware('auth:platform')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('platform.logout');
    
    // ダッシュボード（認証後のデフォルトページ）
    Route::get('/dashboard', function () {
        return view('platform.dashboard');
    })->name('platform.dashboard');

    // オプション管理
    Route::resource('options', OptionController::class)->names([
        'index' => 'admin.options.index',
        'create' => 'admin.options.create',
        'store' => 'admin.options.store',
        'edit' => 'admin.options.edit',
        'update' => 'admin.options.update',
        'destroy' => 'admin.options.destroy'
    ]);

    // 経費管理
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('admin.expenses.store');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('admin.expenses.destroy');
}); 