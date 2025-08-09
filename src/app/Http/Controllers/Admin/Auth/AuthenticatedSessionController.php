<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Carbon\Carbon;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('admin.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // 管理者認証をログアウト
        Auth::guard('admin')->logout();

        // セッションを完全に無効化
        $request->session()->invalidate();

        // CSRFトークンを再生成
        $request->session()->regenerateToken();

        // 車両タイプ料金設定のセッションデータもクリア
        $request->session()->forget('car_type_price_yearly_input');

        // その他の管理者関連セッションデータをクリア
        $request->session()->flush();

        return redirect()->route('admin.login');
    }
}
