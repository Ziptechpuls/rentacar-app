<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // 統計データを取得
        $totalReservations = Reservation::count();
        
        // 今月の売上（仮の計算 - 実際の料金計算ロジックに応じて調整）
        $currentMonthRevenue = Reservation::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_price');
        
        // 今月の予約数
        $currentMonthReservations = Reservation::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        // アクティブユーザー数（一時的に全ユーザー数を表示）
        $activeUsers = User::count();
        
        // 今日の予約（今日出発する予約）
        $todayDepartures = Reservation::whereDate('start_datetime', Carbon::today())
            ->with(['car.carModel', 'user'])
            ->orderBy('start_datetime')
            ->get();
        
        // 今日の返却（今日返却する予約）
        $todayReturns = Reservation::whereDate('end_datetime', Carbon::today())
            ->with(['car.carModel', 'user'])
            ->orderBy('end_datetime')
            ->get();
        
        return view('admin.dashboard', compact(
            'totalReservations',
            'currentMonthRevenue',
            'currentMonthReservations',
            'activeUsers',
            'todayDepartures',
            'todayReturns'
        ));
    }
} 