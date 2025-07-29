<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Reservation;
use App\Models\ExpenseCategory;
use App\Models\ExpenseAmount;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        // 認証済みの管理者のcompany_idを取得
        $companyId = auth('admin')->user()->company_id;

        // 期間フィルター
        $startDate = $request->get('start_date') 
            ? Carbon::parse($request->get('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();
        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))->endOfDay()
            : Carbon::now()->endOfMonth();

        // 基本のクエリ - 確定済みの予約のみを対象とし、会社IDでフィルタリング
        $baseQuery = Reservation::query()
            ->whereHas('car', function($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->where('status', 'confirmed');

        // 統計データ
        $totalRevenue = (clone $baseQuery)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_price');

        $monthlyRevenue = (clone $baseQuery)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_price');

        $monthlyReservations = (clone $baseQuery)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $averagePrice = $monthlyReservations > 0 ? $monthlyRevenue / $monthlyReservations : 0;

        // 月別売上データ（過去12ヶ月）
        $monthlySalesData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlySales = (clone $baseQuery)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('total_price');
            
            $monthlySalesData[] = [
                'month' => $date->format('Y-m'),
                'label' => $date->format('M Y'),
                'sales' => $monthlySales
            ];
        }

        // 車種別売上データ
        $carModelSales = DB::table('reservations')
            ->join('cars', 'reservations.car_id', '=', 'cars.id')
            ->join('car_models', 'cars.car_model_id', '=', 'car_models.id')
            ->where('cars.company_id', $companyId)
            ->where('reservations.status', 'confirmed')
            ->whereBetween('reservations.created_at', [$startDate, $endDate])
            ->select(
                'car_models.name',
                DB::raw('COUNT(reservations.id) as reservation_count'),
                DB::raw('SUM(reservations.total_price) as total_sales')
            )
            ->groupBy('car_models.id', 'car_models.name')
            ->orderBy('total_sales', 'desc')
            ->get();

        // 車種別売上にパーセントを追加
        $totalCarSales = $carModelSales->sum('total_sales');
        $carModelSales = $carModelSales->map(function($item) use ($totalCarSales) {
            $item->percentage = $totalCarSales > 0 ? round(($item->total_sales / $totalCarSales) * 100, 1) : 0;
            return $item;
        });

        // 日別売上データ（過去30日）
        $dailySalesData = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailySales = (clone $baseQuery)
                ->whereDate('created_at', $date)
                ->sum('total_price');
            
            $dailySalesData->push([
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('M d'),
                'sales' => $dailySales
            ]);
        }

        // 売上詳細データ
        $salesDetails = (clone $baseQuery)
            ->with(['car.carModel', 'options'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->paginate(20);

        // 経費データ（データベースから取得）
        $fixedExpenses = ExpenseCategory::where('company_id', $companyId)
            ->where('type', 'fixed')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(function($category) use ($companyId) {
                $amount = ExpenseAmount::where('company_id', $companyId)
                    ->where('expense_category_id', $category->id)
                    ->where('effective_date', '<=', now())
                    ->orderBy('effective_date', 'desc')
                    ->value('amount') ?? 0;
                
                // 小数点を除去して整数に変換
                $amount = (int)$amount;
                
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'amount' => $amount
                ];
            })
            ->toArray();
        
        $variableExpenses = ExpenseCategory::where('company_id', $companyId)
            ->where('type', 'variable')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(function($category) use ($companyId) {
                $amount = ExpenseAmount::where('company_id', $companyId)
                    ->where('expense_category_id', $category->id)
                    ->where('effective_date', '<=', now())
                    ->orderBy('effective_date', 'desc')
                    ->value('amount') ?? 0;
                
                // 小数点を除去して整数に変換
                $amount = (int)$amount;
                
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'amount' => $amount
                ];
            })
            ->toArray();
        
        // 総経費計算
        $totalFixedExpenses = collect($fixedExpenses)->sum('amount');
        $totalVariableExpenses = collect($variableExpenses)->sum('amount');
        $totalExpenses = $totalFixedExpenses + $totalVariableExpenses;
        
        // 利益計算
        $grossProfit = $totalRevenue - $totalExpenses;
        $netProfit = $grossProfit; // 簡易版なので粗利と純利益は同じ

        // 年と月の変数を追加
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);

        return view('admin.sales.index', compact(
            'totalRevenue',
            'monthlyRevenue',
            'monthlyReservations',
            'averagePrice',
            'monthlySalesData',
            'carModelSales',
            'dailySalesData',
            'fixedExpenses',
            'variableExpenses',
            'totalFixedExpenses',
            'totalVariableExpenses',
            'totalExpenses',
            'grossProfit',
            'netProfit',
            'startDate',
            'endDate',
            'year',
            'month'
        ));
    }
} 