<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Option;
use App\Models\Company;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class CarController extends Controller
{
    /**
     * 営業時間から出発時間と返却時間のデフォルト値を計算
     */
    private function calculateDefaultTimes($businessHours)
    {
        // 営業時間の形式: "8:00〜20:00" または "9:00-18:00" など
        $pattern = '/(\d{1,2}):(\d{2})[〜\-~]\s*(\d{1,2}):(\d{2})/u';
        
        if (preg_match($pattern, $businessHours, $matches)) {
            $openHour = (int)$matches[1];
            $openMinute = (int)$matches[2];
            $closeHour = (int)$matches[3];
            $closeMinute = (int)$matches[4];
            
            // デバッグ用（本番環境では削除）
            Log::info('Parsed hours: ' . $openHour . ':' . $openMinute . ' - ' . $closeHour . ':' . $closeMinute);
            
            // 出発時間: 営業開始時間をそのまま使用
            $departureHour = $openHour;
            $departureMinute = $openMinute;
            
            // 返却時間: 営業終了時間をそのまま使用
            $returnHour = $closeHour;
            $returnMinute = $closeMinute;
            
            // 5分刻みに調整
            $departureMinute = round($departureMinute / 5) * 5;
            $returnMinute = round($returnMinute / 5) * 5;
            
            // 分が60になった場合は時間に繰り上げ
            if ($departureMinute >= 60) {
                $departureHour += 1;
                $departureMinute = 0;
            }
            if ($returnMinute >= 60) {
                $returnHour += 1;
                $returnMinute = 0;
            }
            
            $result = [
                'start' => sprintf('%02d:%02d', $departureHour, $departureMinute),
                'end' => sprintf('%02d:%02d', $returnHour, $returnMinute)
            ];
            
            // デバッグ用（本番環境では削除）
            Log::info('Calculated result: ' . json_encode($result));
            
            return $result;
        }
        
        // デバッグ用（本番環境では削除）
        Log::info('Business hours parsing failed for: ' . $businessHours);
        
        // デフォルト値（営業時間が解析できない場合）
        return ['start' => '09:00', 'end' => '18:00'];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // ------------------------
        // 1. 日時の処理
        // ------------------------
        $startDateTime = null;
        $endDateTime = null;

        if ($request->filled('start_date') && $request->filled('start_time')) {
            $startInput = $request->input('start_date') . ' ' . $request->input('start_time');
            try {
                $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $startInput);
            } catch (\Exception $e) {
                $startDateTime = null; // フォーマットエラー時はnull
            }
        }

        if ($request->filled('end_date') && $request->filled('end_time')) {
            $endInput = $request->input('end_date') . ' ' . $request->input('end_time');
            try {
                $endDateTime = Carbon::createFromFormat('Y-m-d H:i', $endInput);
            } catch (\Exception $e) {
                $endDateTime = null;
            }
        }

        // ------------------------
        // 2. クエリ構築
        // ------------------------
        $query = Car::query()
            ->public() // 公開設定が有効な車両のみ
            ->when($request->filled('company_id'), function ($query) use ($request) {
                return $query->byCompany($request->input('company_id'));
            });

        // 車種フィルター
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // 乗車人数フィルター（以上）
        if ($request->filled('capacity')) {
            $query->where('capacity', '>=', $request->input('capacity'));
        }

        // 予約重複チェック: 指定された期間に予約が入っていない車のみを対象とする
        if ($startDateTime && $endDateTime && $endDateTime->gt($startDateTime)) {
            $query->whereDoesntHave('reservations', function ($q) use ($startDateTime, $endDateTime) {
                $q->where('status', '!=', 'cancelled') // キャンセル以外の全ステータスを考慮（ReservationControllerと統一）
                  ->where(function ($subQuery) use ($startDateTime, $endDateTime) {
                    // 既存の予約が指定期間と少しでも重なる場合は除外
                    // 条件: (予約開始 < 指定終了 AND 予約終了 > 指定開始)
                    $subQuery->where('start_datetime', '<', $endDateTime)
                             ->where('end_datetime', '>', $startDateTime);
                });
            });
        }

        // 並び替え
        switch ($request->input('sort')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'capacity_desc':
                $query->orderBy('capacity', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // 関連データと一緒に取得
        $cars = $query->with(['images', 'company', 'carModel'])->paginate(10)->through(function ($car) use ($startDateTime, $endDateTime) {
            // ページネーションされた各車両に対して料金計算ロジックを適用
            if ($startDateTime && $endDateTime && $endDateTime->gt($startDateTime)) {
                // 日数・泊数の計算
                $isSameDay = $startDateTime->isSameDay($endDateTime);
                
                if ($isSameDay) {
                    // 同日の場合は、時間に関係なく1日計算
                    $days = 1;
                    $nights = 0;
                } else {
                    // 日を跨ぐ場合は、日数+1で計算
                    $days = $startDateTime->diffInDays($endDateTime) + 1;
                    $nights = $startDateTime->diffInDays($endDateTime);
                    
                    // 整数に変換
                    $days = (int)$days;
                    $nights = (int)$nights;
                }
                
                $car->totalPrice = $car->price * $days;

                if ($isSameDay) {
                    $car->durationLabel = '日帰り';
                } else {
                    $car->durationLabel = "{$nights}泊{$days}日";
                }
            } else {
                // 日数・泊数・同日判定・合計料金のデフォルト値を設定
                $isSameDay = true;
                $totalPrice = $car->price;            
            }

            return $car;
        });

        // 会社一覧を取得（フィルター用）
        $companies = \App\Models\Company::has('cars')->get();

        // 営業時間からデフォルト時間を取得
        $defaultTimes = ['start' => '09:00', 'end' => '18:00'];
        
        // データベースから営業時間を取得
        $shop = Shop::first();
        $businessHours = $shop ? $shop->business_hours : null;
        
        // デバッグ用（本番環境では削除）
        Log::info('Shop found: ' . ($shop ? 'yes' : 'no'));
        Log::info('Business hours from DB: ' . ($businessHours ?? 'null'));
        
        $defaultTimes = ['start' => '09:00', 'end' => '18:00']; // デフォルト値
        
        if ($businessHours) {
            $defaultTimes = $this->calculateDefaultTimes($businessHours);
            Log::info('Default times: ' . json_encode($defaultTimes));
        }

        // ------------------------
        // 3. 表示
        // ------------------------
        // 営業時間を取得（Bladeテンプレートで使用）
        $businessHours = $shop ? $shop->business_hours : null;
        $parsedBusinessHours = null;
        if ($businessHours) {
            $parsedBusinessHours = $this->calculateDefaultTimes($businessHours);
        }
        
        return view('user.cars.index', [
            'cars' => $cars,
            'companies' => $companies,
            'startDateTime' => $startDateTime,
            'endDateTime' => $endDateTime,
            'defaultStartTime' => $defaultTimes['start'],
            'defaultEndTime' => $defaultTimes['end'],
            'businessHours' => $businessHours,
            'parsedBusinessHours' => $parsedBusinessHours,
        ]);
    }    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, Request $request)
    {
        $car = Car::with(['images', 'carModel', 'reservations'])->findOrFail($id);
        $options = Option::all();

        // クエリから受け取った日時文字列
        $startStr = $request->query('start_datetime');
        $endStr = $request->query('end_datetime');

        // Carbonオブジェクトに変換（nullable）
        $start = $startStr ? Carbon::parse($startStr) : null;
        $end = $endStr ? Carbon::parse($endStr) : null;

        // 日数・泊数・同日判定・合計料金初期化
        $days = 1;
        $nights = 0;
        $isSameDay = false;
        $totalPrice = 0;

        if ($start && $end && $end->gt($start)) {
            // 日数・泊数の計算
            $isSameDay = $start->isSameDay($end);
            
            if ($isSameDay) {
                // 同日の場合は、時間に関係なく1日計算
                $days = 1;
                $nights = 0;
            } else {
                // 日を跨ぐ場合は、日数+1で計算
                $days = $start->diffInDays($end) + 1;
                $nights = $start->diffInDays($end);
                
                // 整数に変換
                $days = (int)$days;
                $nights = (int)$nights;
            }

            $totalPrice = $car->price * $days;
        } else {
            // 日付指定がなければ日帰り1日分の料金
            $totalPrice = $car->price;
        }

        return view('user.cars.show', [
            'car' => $car,
            'options' => $options,
            'start' => $start,
            'end' => $end,
            'days' => $days,
            'nights' => $nights,
            'isSameDay' => $isSameDay,
            'totalPrice' => $totalPrice,
        ]);
    }   
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
