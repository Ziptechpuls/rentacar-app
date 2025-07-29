<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // 名前または電話番号検索
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('reservations', function ($reservationQuery) use ($search) {
                      $reservationQuery->where('phone_main', 'like', "%{$search}%");
                  });
            });
        }

        // 予約日付フィルタ（日付範囲）
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        if ($startDate && $endDate && $startDate === $endDate) {
            // 同じ日の場合：その日出発の予約を検索
            $query->whereHas('reservations', function ($q) use ($startDate) {
                $q->whereDate('start_datetime', '=', $startDate);
            });
        } else {
            // 異なる日または片方のみの場合：範囲検索
            if ($startDate) {
                $query->whereHas('reservations', function ($q) use ($startDate) {
                    $q->whereDate('start_datetime', '>=', $startDate);
                });
            }
            if ($endDate) {
                $query->whereHas('reservations', function ($q) use ($endDate) {
                    $q->whereDate('end_datetime', '<=', $endDate);
                });
            }
        }

        // ページネーションで取得
        $customers = $query->with(['reservations' => function ($q) use ($startDate, $endDate) {
            if ($startDate && $endDate && $startDate === $endDate) {
                // 同じ日の場合：その日出発の予約のみ
                $q->whereDate('start_datetime', '=', $startDate);
            } else {
                // 異なる日または片方のみの場合：範囲検索
                if ($startDate) {
                    $q->whereDate('start_datetime', '>=', $startDate);
                }
                if ($endDate) {
                    $q->whereDate('end_datetime', '<=', $endDate);
                }
            }
            $q->with(['car.carModel']);
        }])->get();

        // 各ユーザーの最新の予約から電話番号とオプションを取得
        $customersWithPhone = $customers->map(function ($customer) {
            $latestReservation = $customer->reservations->sortByDesc('created_at')->first();
            $customer->phone_main = $latestReservation ? $latestReservation->phone_main : null;
            $customer->latest_options = $latestReservation ? $latestReservation->options : collect();
            return $customer;
        });

        // ページネーションを手動で実装
        $perPage = 15;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $customers = new \Illuminate\Pagination\LengthAwarePaginator(
            $customersWithPhone->slice($offset, $perPage),
            $customersWithPhone->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        // 顧客の詳細情報と予約履歴を取得
        $customer->load(['reservations' => function ($query) {
            $query->with(['car.carModel'])
                  ->orderBy('start_datetime', 'desc');
        }]);

        // 統計情報を計算
        $totalReservations = $customer->reservations->count();
        $totalSpent = $customer->reservations->sum('total_price');
        $averageReservationValue = $totalReservations > 0 ? $totalSpent / $totalReservations : 0;
        
        // 最近の予約（過去6ヶ月）
        $recentReservations = $customer->reservations()
            ->where('start_datetime', '>=', now()->subMonths(6))
            ->with(['car.carModel'])
            ->orderBy('start_datetime', 'desc')
            ->get();

        return view('admin.customers.show', compact('customer', 'totalReservations', 'totalSpent', 'averageReservationValue', 'recentReservations'));
    }
}