<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarTypePrice;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CarTypePriceController extends Controller
{
    public function index()
    {
        $carTypePrices = CarTypePrice::orderBy('car_type')
                                    ->orderBy('start_date')
                                    ->get()
                                    ->groupBy('car_type');
        
        // 車両タイプの表示順序を固定
        $orderedCarTypes = ['軽自動車', 'セダン', 'SUV', 'ミニバン', 'コンパクト', 'ステーションワゴン', 'その他'];
        $orderedCarTypePrices = [];
        
        foreach ($orderedCarTypes as $carType) {
            if (isset($carTypePrices[$carType])) {
                $orderedCarTypePrices[$carType] = $carTypePrices[$carType];
            }
        }
        
        $carTypePrices = $orderedCarTypePrices;
        
        // 各車両タイプのシーズン料金を取得
        $seasonPrices = [];
        foreach ($carTypePrices as $carType => $prices) {
            $seasonPrices[$carType] = [
                'high_season' => CarTypePrice::where('car_type', $carType)
                    ->where('season_type', 'high_season')
                    ->whereNull('start_date')
                    ->whereNull('end_date')
                    ->where('is_active', true)
                    ->whereNotNull('price_per_day')
                    ->where('price_per_day', '>', 0)
                    ->select('id', 'car_type', 'season_type', 'price_per_day', 'period_name', 'is_active')
                    ->first(),
                'normal' => CarTypePrice::where('car_type', $carType)
                    ->where('season_type', 'normal')
                    ->whereNull('start_date')
                    ->whereNull('end_date')
                    ->where('is_active', true)
                    ->whereNotNull('price_per_day')
                    ->where('price_per_day', '>', 0)
                    ->select('id', 'car_type', 'season_type', 'price_per_day', 'period_name', 'is_active')
                    ->first(),
                'low_season' => CarTypePrice::where('car_type', $carType)
                    ->where('season_type', 'low_season')
                    ->whereNull('start_date')
                    ->whereNull('end_date')
                    ->where('is_active', true)
                    ->whereNotNull('price_per_day')
                    ->where('price_per_day', '>', 0)
                    ->select('id', 'car_type', 'season_type', 'price_per_day', 'period_name', 'is_active')
                    ->first(),
            ];
        }
        
        return view('admin.car-type-prices.index', compact('carTypePrices', 'seasonPrices'));
    }

    public function edit(CarTypePrice $carTypePrice)
    {
        $carTypes = [
            '軽自動車' => '軽自動車',
            'セダン' => 'セダン',
            'SUV' => 'SUV',
            'ミニバン' => 'ミニバン',
            'コンパクト' => 'コンパクト',
            'ステーションワゴン' => 'ステーションワゴン',
            'その他' => 'その他',
        ];

        return view('admin.car-type-prices.edit', compact('carTypePrice', 'carTypes'));
    }

    public function update(Request $request, CarTypePrice $carTypePrice)
    {
        $request->validate([
            'car_type' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'season_type' => 'required|string|in:high_season,normal,low_season',
            'price_per_day' => 'required|integer|min:1|max:999999',
            'period_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ], [
            'price_per_day.required' => '料金は必須です。',
            'price_per_day.min' => '料金は1円以上で入力してください。',
            'price_per_day.max' => '料金は999,999円以下で入力してください。',
        ]);

        $carTypePrice->update($request->all());

        return redirect()->route('admin.car-type-prices.index')
            ->with('success', '車両タイプ別料金を更新しました。');
    }

    public function destroy(CarTypePrice $carTypePrice)
    {
        $carTypePrice->delete();

        return redirect()->route('admin.car-type-prices.index')
            ->with('success', '車両タイプ別料金を削除しました。');
    }

    public function toggleActive(CarTypePrice $carTypePrice)
    {
        $carTypePrice->update(['is_active' => !$carTypePrice->is_active]);

        $status = $carTypePrice->is_active ? '有効' : '無効';
        return redirect()->route('admin.car-type-prices.index')
            ->with('success', "車両タイプ別料金を{$status}にしました。");
    }

    // 年間料金設定の一括作成
    public function createYearly(Request $request)
    {
        $carTypes = [
            '軽自動車' => '軽自動車',
            'セダン' => 'セダン',
            'SUV' => 'SUV',
            'ミニバン' => 'ミニバン',
            'コンパクト' => 'コンパクト',
            'ステーションワゴン' => 'ステーションワゴン',
            'その他' => 'その他',
        ];

        // リクエストパラメータから車両タイプと年を取得
        $selectedCarType = $request->get('car_type', '軽自動車');
        $selectedYear = $request->get('year', date('Y'));

        // 既存のデータを取得（指定年の期間設定）
        $existingData = CarTypePrice::where('car_type', $selectedCarType)
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->whereYear('start_date', $selectedYear)
            ->orderBy('start_date')
            ->get();

        // 既存のシーズン料金データを取得
        $existingSeasonPrices = CarTypePrice::where('car_type', $selectedCarType)
            ->whereNull('start_date')
            ->whereNull('end_date')
            ->where('is_active', true)
            ->get()
            ->keyBy('season_type');

        // 前回の入力値を取得（セッションから）
        $previousInput = session('car_type_price_yearly_input', []);

        return view('admin.car-type-prices.create-yearly', compact(
            'carTypes', 
            'existingData', 
            'existingSeasonPrices', 
            'selectedCarType', 
            'selectedYear',
            'previousInput'
        ));
    }

    // 既存データを取得するAPI
    public function getExistingData(Request $request)
    {
        $carType = $request->get('car_type');
        $year = $request->get('year', date('Y'));

        if (!$carType) {
            return response()->json([]);
        }

        // 指定年の期間設定データを取得
        $existingData = CarTypePrice::where('car_type', $carType)
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->whereYear('start_date', $year)
            ->orderBy('start_date')
            ->get()
            ->map(function ($data) {
                return [
                    'id' => $data->id,
                    'start_date' => $data->start_date->format('Y/m/d'),
                    'end_date' => $data->end_date->format('Y/m/d'),
                    'season_name' => $data->getSeasonDisplayName(),
                    'price_per_day' => $data->price_per_day,
                    'formatted_price' => '¥' . number_format($data->price_per_day) . '/日',
                    'edit_url' => route('admin.car-type-prices.edit', $data),
                ];
            });

        return response()->json($existingData);
    }

    public function storeYearly(Request $request)
    {
        $request->validate([
            'car_type' => 'required|string|max:255',
            'year' => 'required|integer|min:2024|max:2030',
            'periods' => 'required|array|min:1',
            'periods.*.start_date' => 'required|date',
            'periods.*.end_date' => 'required|date|after_or_equal:periods.*.start_date',
            'periods.*.season_type' => 'required|string|in:high_season,normal,low_season',
            'season_prices' => 'required|array',
            'season_prices.high_season' => 'required|array',
            'season_prices.high_season.price_per_day' => 'required|integer|min:1|max:999999',
            'season_prices.normal' => 'required|array',
            'season_prices.normal.price_per_day' => 'required|integer|min:1|max:999999',
            'season_prices.low_season' => 'required|array',
            'season_prices.low_season.price_per_day' => 'required|integer|min:1|max:999999',
        ], [
            'season_prices.high_season.price_per_day.required' => 'ハイシーズン料金は必須です。',
            'season_prices.high_season.price_per_day.min' => 'ハイシーズン料金は1円以上で入力してください。',
            'season_prices.normal.price_per_day.required' => '通常料金は必須です。',
            'season_prices.normal.price_per_day.min' => '通常料金は1円以上で入力してください。',
            'season_prices.low_season.price_per_day.required' => 'ローシーズン料金は必須です。',
            'season_prices.low_season.price_per_day.min' => 'ローシーズン料金は1円以上で入力してください。',
            'periods.required' => 'シーズン料金を設定した場合は、期間設定が必須です。',
            'periods.min' => 'シーズン料金を設定した場合は、少なくとも1つの期間設定が必要です。',
        ]);

        $year = $request->year;
        $carType = $request->car_type;
        
        // 期間の重複チェック
        $periods = $request->periods;
        $overlappingPeriods = $this->checkOverlappingPeriods($periods);
        
        if (!empty($overlappingPeriods)) {
            return back()->withErrors([
                'periods' => '期間が重複しています: ' . implode(', ', $overlappingPeriods)
            ])->withInput();
        }
        
        // 既存データとの重複チェック（更新モードの場合は除外）
        $existingOverlaps = $this->checkExistingOverlaps($periods, $carType, $year);
        if (!empty($existingOverlaps)) {
            // 重複がある場合は既存データを削除してから新規作成
            $this->deleteExistingPeriods($carType, $year);
        }

        // シーズン料金を保存
        $seasonPrices = $request->season_prices;
        foreach ($seasonPrices as $seasonType => $seasonData) {
            // シーズン料金を保存（season_pricesテーブルがないので、car_type_pricesテーブルに保存）
            CarTypePrice::updateOrCreate(
                [
                    'car_type' => $carType,
                    'season_type' => $seasonType,
                    'start_date' => null,
                    'end_date' => null,
                ],
                [
                    'price_per_day' => $seasonData['price_per_day'],
                    'period_name' => $seasonType,
                    'is_active' => true,
                ]
            );
        }

        // 期間設定を保存
        foreach ($request->periods as $period) {
            // 該当するシーズン料金を取得
            $seasonPrice = $seasonPrices[$period['season_type']]['price_per_day'] ?? null;
            
            if (!$seasonPrice) {
                return back()->withErrors([
                    'periods' => "期間設定で指定されたシーズン（{$period['season_type']}）の料金が設定されていません。"
                ])->withInput();
            }
            
            // 既存のデータがあるかチェック
            $existingPrice = CarTypePrice::where('car_type', $carType)
                ->where('start_date', $period['start_date'])
                ->where('end_date', $period['end_date'])
                ->first();
            
            if ($existingPrice) {
                // 既存のデータを更新
                $existingPrice->update([
                    'season_type' => $period['season_type'],
                    'price_per_day' => $seasonPrice,
                    'period_name' => $period['start_date'] . ' - ' . $period['end_date'],
                    'comment' => $period['comment'] ?? null,
                    'is_active' => true,
                ]);
            } else {
                // 新しいデータを作成
                CarTypePrice::create([
                    'car_type' => $carType,
                    'start_date' => $period['start_date'],
                    'end_date' => $period['end_date'],
                    'season_type' => $period['season_type'],
                    'price_per_day' => $seasonPrice,
                    'period_name' => $period['start_date'] . ' - ' . $period['end_date'],
                    'comment' => $period['comment'] ?? null,
                    'notes' => null,
                    'is_active' => true,
                ]);
            }
        }

        // 前回の入力値をセッションに保存
        session([
            'car_type_price_yearly_input' => [
                'car_type' => $carType,
                'year' => $year,
                'season_prices' => $seasonPrices,
                'periods' => $request->periods,
            ]
        ]);

        return redirect()->route('admin.car-type-prices.index')
            ->with('success', "{$year}年の{$carType}の料金設定を一括作成しました。");
    }



    /**
     * 期間の重複をチェックする
     */
    private function checkOverlappingPeriods($periods)
    {
        $overlappingPeriods = [];
        
        // 期間を開始日でソート
        usort($periods, function($a, $b) {
            return Carbon::parse($a['start_date']) <=> Carbon::parse($b['start_date']);
        });
        
        for ($i = 0; $i < count($periods); $i++) {
            for ($j = $i + 1; $j < count($periods); $j++) {
                $period1 = $periods[$i];
                $period2 = $periods[$j];
                
                $start1 = Carbon::parse($period1['start_date']);
                $end1 = Carbon::parse($period1['end_date']);
                $start2 = Carbon::parse($period2['start_date']);
                $end2 = Carbon::parse($period2['end_date']);
                
                // 期間が重複しているかチェック
                if (($start1 <= $end2) && ($end1 >= $start2)) {
                    $overlappingPeriods[] = "期間" . ($i + 1) . "と期間" . ($j + 1);
                }
            }
        }
        
        return $overlappingPeriods;
    }

    /**
     * 既存データとの重複をチェックする
     */
    private function checkExistingOverlaps($periods, $carType, $year)
    {
        $existingOverlaps = [];
        
        // 指定された年と車両タイプの既存データを取得
        $existingPrices = CarTypePrice::where('car_type', $carType)
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->where('is_active', true)
            ->get();
        
        foreach ($periods as $index => $period) {
            $newStart = Carbon::parse($period['start_date']);
            $newEnd = Carbon::parse($period['end_date']);
            
            foreach ($existingPrices as $existing) {
                $existingStart = Carbon::parse($existing->start_date);
                $existingEnd = Carbon::parse($existing->end_date);
                
                // 期間が重複しているかチェック
                if (($newStart <= $existingEnd) && ($newEnd >= $existingStart)) {
                    $existingOverlaps[] = "期間" . ($index + 1) . "と既存期間（{$existing->start_date} - {$existing->end_date}）";
                }
            }
        }
        
        return $existingOverlaps;
    }

    /**
     * 既存の期間データを削除する
     */
    private function deleteExistingPeriods($carType, $year)
    {
        // 指定された年と車両タイプの既存データを削除
        CarTypePrice::where('car_type', $carType)
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->where('is_active', true)
            ->delete();
    }

    // 車両タイプと日付で料金を取得するAPI
    public function getPriceByTypeAndDate(Request $request)
    {
        $request->validate([
            'car_type' => 'required|string',
            'date' => 'required|date',
        ]);

        $price = CarTypePrice::getPriceByTypeAndDate(
            $request->car_type,
            $request->date
        );

        return response()->json([
            'price' => $price
        ]);
    }



    // シーズン料金を取得するAPI
    public function getSeasonPrices(Request $request)
    {
        $request->validate([
            'car_type' => 'required|string',
        ]);

        $seasonPrices = CarTypePrice::where('car_type', $request->car_type)
            ->whereNull('start_date')
            ->whereNull('end_date')
            ->where('is_active', true)
            ->get()
            ->keyBy('season_type')
            ->map(function($item) {
                return [
                    'price_per_day' => $item->price_per_day,
                    'season_type' => $item->season_type,
                ];
            });

        return response()->json($seasonPrices);
    }
}
