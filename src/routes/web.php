<?php

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController as AdminAuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\RegisteredUserController as AdminRegisteredUserController;
use App\Http\Controllers\Admin\Auth\ProfileController as AdminProfileController; 
use App\Http\Controllers\Admin\CarController as AdminCarController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReservationController as AdminReservationController;
use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\User\Auth\ProfileController as UserProfileController; 
use App\Http\Controllers\User\CarController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\ReservationController;
use App\Http\Controllers\Admin\PriceController;
use App\Http\Controllers\Admin\PrivacyController;
use App\Http\Controllers\Admin\TermsController;
use App\Http\Controllers\Admin\CancelController;
use App\Http\Controllers\Admin\OptionController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\ExpenseCategoryController;
use App\Http\Controllers\Admin\CarTypePriceController;
use App\Http\Controllers\Admin\SeasonPriceController;


// ユーザー側のトップページ
Route::get('/', function () {
    return view('welcome');
});

// プラットフォーム管理者用ルートは別ドメインで処理するため、ここでは定義しない

// 管理者側ルーティング
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.login'));

    // 未認証の管理者向け
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [AdminAuthenticatedSessionController::class, 'store']);

        // 管理者登録画面表示
        Route::get('/register', [AdminRegisteredUserController::class, 'create'])->name('register');
        Route::post('/register', [AdminRegisteredUserController::class, 'store'])->name('register.store'); // 登録処理
    });

    // 認証済みの管理者向け
    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // プロフィール管理
        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [AdminProfileController::class, 'destroy'])->name('profile.destroy');

        // 車両管理
        Route::resource('cars', AdminCarController::class)->except(['show']); 
        Route::get('cars/{car}', [AdminCarController::class, 'show'])->name('cars.show');
        Route::patch('cars/{car}/toggle-publish', [AdminCarController::class, 'togglePublish'])->name('cars.togglePublish');

        // 予約管理
        Route::resource('reservations', AdminReservationController::class);
        Route::get('reservations/create', [AdminReservationController::class, 'create'])->name('reservations.create');
        Route::post('reservations', [AdminReservationController::class, 'store'])->name('reservations.store');

        // カレンダー管理（一時的に無効化）
        // Route::get('calendar', [App\Http\Controllers\Admin\CalendarController::class, 'index'])->name('calendar.index');
        // Route::get('calendar/reservations', [App\Http\Controllers\Admin\CalendarController::class, 'getReservations'])->name('calendar.reservations');

        // 顧客管理
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

        // 売上管理
        Route::get('/reports', [SalesController::class, 'index'])->name('reports.sales');

        // 経費項目管理
        Route::resource('expense-categories', ExpenseCategoryController::class)->names([
            'index' => 'expense-categories.index',
            'store' => 'expense-categories.store',
            'update' => 'expense-categories.update',
            'destroy' => 'expense-categories.destroy',
        ]);
        Route::patch('expense-categories/{expenseCategory}/toggle-active', [ExpenseCategoryController::class, 'toggleActive'])->name('expense-categories.toggle-active');
        Route::post('expense-amounts', [ExpenseCategoryController::class, 'storeAmount'])->name('expense-amounts.store');

        // 料金管理
        Route::prefix('price')->name('price.')->group(function () {
            Route::get('/', [PriceController::class, 'index'])->name('index');
            
            // シーズン管理
            Route::prefix('season')->name('season.')->group(function () {
                Route::get('/create', [PriceController::class, 'seasonCreate'])->name('create');
                Route::post('/', [PriceController::class, 'seasonStore'])->name('store');
                Route::get('/{season}/edit', [PriceController::class, 'seasonEdit'])->name('edit');
                Route::put('/{season}', [PriceController::class, 'seasonUpdate'])->name('update');
                Route::delete('/{season}', [PriceController::class, 'seasonDestroy'])->name('destroy');
            });

            // 車種別料金管理
            Route::prefix('car')->name('car.')->group(function () {
                Route::get('/create', [PriceController::class, 'carCreate'])->name('create');
                Route::post('/', [PriceController::class, 'carStore'])->name('store');
                Route::get('/{price}/edit', [PriceController::class, 'carEdit'])->name('edit');
                Route::put('/{price}', [PriceController::class, 'carUpdate'])->name('update');
                Route::delete('/{price}', [PriceController::class, 'carDestroy'])->name('destroy');
            });
        });

        // プライバシーポリシー
        Route::get('privacy', [PrivacyController::class, 'index'])->name('privacy.index');
        Route::get('privacy/edit', [PrivacyController::class, 'edit'])->name('privacy.edit');
        Route::put('/privacy', [PrivacyController::class, 'update'])->name('privacy.update');

        // 利用規約
        Route::get('terms', [TermsController::class, 'index'])->name('terms.index');
        Route::get('terms/edit', [TermsController::class, 'edit'])->name('terms.edit');
        Route::put('/terms', [TermsController::class, 'update'])->name('terms.update');

        // キャンセルポリシー
        Route::get('cancel', [CancelController::class, 'index'])->name('cancel.index');
        Route::get('cancel/edit', [CancelController::class, 'edit'])->name('cancel.edit');
        Route::put('/cancel', [CancelController::class, 'update'])->name('cancel.update');

        // オプション設定
        Route::resource('options', OptionController::class);
        Route::patch('options/sort-order', [OptionController::class, 'updateSortOrder'])->name('options.updateSortOrder');

        // 店舗情報
        Route::get('shop', [ShopController::class, 'index'])->name('shop.index');
        Route::get('shop/edit', [ShopController::class, 'edit'])->name('shop.edit');
        Route::put('/shop', [ShopController::class, 'update'])->name('shop.update');

        // 車両タイプ別料金管理
        Route::get('car-type-prices', [CarTypePriceController::class, 'index'])->name('car-type-prices.index');
        Route::get('car-type-prices/create-yearly', [CarTypePriceController::class, 'createYearly'])->name('car-type-prices.create-yearly');
        Route::post('car-type-prices/store-yearly', [CarTypePriceController::class, 'storeYearly'])->name('car-type-prices.store-yearly');
        Route::get('car-type-prices/get-existing-data', [CarTypePriceController::class, 'getExistingData'])->name('car-type-prices.get-existing-data');
        Route::get('car-type-prices/get-season-prices', [CarTypePriceController::class, 'getSeasonPrices'])->name('car-type-prices.get-season-prices');
        Route::get('car-type-prices/{carTypePrice}/edit', [CarTypePriceController::class, 'edit'])->name('car-type-prices.edit');
        Route::put('car-type-prices/{carTypePrice}', [CarTypePriceController::class, 'update'])->name('car-type-prices.update');
        Route::delete('car-type-prices/{carTypePrice}', [CarTypePriceController::class, 'destroy'])->name('car-type-prices.destroy');
        Route::patch('car-type-prices/{carTypePrice}/toggle-active', [CarTypePriceController::class, 'toggleActive'])->name('car-type-prices.toggle-active');
        Route::get('car-type-prices/get-price-by-type-and-date', [CarTypePriceController::class, 'getPriceByTypeAndDate'])->name('car-type-prices.get-price-by-type-and-date');





        // 管理者ログアウト
        Route::post('/logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('logout');
    });
});

// ユーザー側
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
});

// 1. 空車一覧（検索結果）
Route::get('/cars', [CarController::class, 'index'])->name('user.cars.index');

// 2. 車の詳細画面
Route::get('/cars/{car}', [CarController::class, 'show'])->name('user.cars.show');

// 3～7. 予約フロー（car単位にまとめる）
Route::prefix('/cars/{car}/reservations')->name('user.cars.reservations.')->group(function () {

    // ★変更：ステップ1-A：オプション等確認画面表示 (GET)
    Route::get('option-confirm', [ReservationController::class, 'showOptionConfirm'])
        ->name('show-option-confirm');

    // ステップ1-B：オプション等確認後、セッション保存しお客様情報入力へ (POST)
    Route::post('car-confirm', [ReservationController::class, 'carConfirm'])
        ->name('car-confirm');

    // ステップ2：お客様情報入力画面表示
    Route::get('input', [ReservationController::class, 'input'])
        ->name('input');

    // ステップ3：お客様情報を保存し、最終確認画面へ
    Route::post('confirm-customer', [ReservationController::class, 'confirmCustomer'])
        ->name('confirm-customer');

    // ステップ4：最終確認画面表示
    Route::get('final-confirm', [ReservationController::class, 'finalConfirm'])
        ->name('final-confirm');

    // ステップ5：予約完了処理
    Route::post('reserved', [ReservationController::class, 'reserved'])
        ->name('reserved');

    // ステップ6：予約完了画面表示
    Route::get('complete/{reservation}', [ReservationController::class, 'complete'])
        ->name('complete');
});

// 料金表
Route::get('/pricing', [\App\Http\Controllers\User\StoreController::class, 'pricing'])->name('store.pricing');

// 店舗情報
Route::get('/store-info', function () {
    $shop = \App\Models\Shop::first();
    return view('user.store.info', compact('shop'));
})->name('store.info');

// ログイン後のユーザー画面
Route::get('/mypage', function () {
    return view('user.mypage');
})->middleware(['auth', 'verified'])->name('mypage');

// ログイン後のユーザー画面の編集、更新、削除
Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [UserProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [UserProfileController::class, 'destroy'])->name('profile.destroy');
});

// ログイン不要で予約可能
Route::prefix('user')->name('user.')->group(function () {
    Route::get('/price', [\App\Http\Controllers\User\PriceController::class, 'index'])->name('price.index');
    Route::post('/reservations/confirm', [ReservationController::class, 'confirm'])->name('reservations.confirm');
});

// ポリシー関連のルート (ユーザー側フッター)
Route::prefix('policies')->name('user.')->group(function () {
    Route::get('/privacy', function () {
        $policy = \App\Models\Policy::where('type', 'privacy')->first();
        return view('user.policies.show', ['policy' => $policy, 'title' => 'プライバシーポリシー']);
    })->name('privacy');

    Route::get('/terms', function () {
        $policy = \App\Models\Policy::where('type', 'terms')->first();
        return view('user.policies.show', ['policy' => $policy, 'title' => '利用規約']);
    })->name('terms');

    Route::get('/cancel', function () {
        $policy = \App\Models\Policy::where('type', 'cancel')->first();
        return view('user.policies.show', ['policy' => $policy, 'title' => 'キャンセルポリシー']);
          })->name('cancel');
});





require __DIR__.'/auth.php';
