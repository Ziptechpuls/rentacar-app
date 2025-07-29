<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Car;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReservationController extends Controller
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
            
            return $result;
        }
        
        // デフォルト値（営業時間が解析できない場合）
        return ['start' => '09:00', 'end' => '18:00'];
    }

    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        
        // 指定された月の開始日と終了日
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        
        // 車両データを取得（予約情報付き）- 簡素化版
        $cars = Car::with(['carModel', 'reservations' => function ($query) {
            $query->where('status', '!=', 'cancelled');
        }])->get();

        // 従来の予約一覧（ページネーション用）
        $query = Reservation::with(['car.carModel', 'user']);

        // 顧客名での検索
        if ($request->filled('customer_name')) {
            $customerName = $request->get('customer_name');
            $query->where(function ($q) use ($customerName) {
                $q->where('name_kanji', 'like', "%{$customerName}%")
                  ->orWhere('email', 'like', "%{$customerName}%")
                  ->orWhereHas('user', function ($userQuery) use ($customerName) {
                      $userQuery->where('name', 'like', "%{$customerName}%");
                  });
            });
        }

        // ステータスでのフィルター
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // 開始日でのフィルター
        if ($request->filled('start_date')) {
            $query->whereDate('start_datetime', '>=', $request->get('start_date'));
        }

        // 終了日でのフィルター
        if ($request->filled('end_date')) {
            $query->whereDate('end_datetime', '<=', $request->get('end_date'));
        }

        $reservations = $query->latest()->paginate(20);
        
        // 営業時間を取得（User側と同じロジック）
        $shop = \App\Models\Shop::first();
        $businessHours = $shop ? $shop->business_hours : null;
        $parsedBusinessHours = null;
        
        if ($businessHours) {
            $parsedBusinessHours = $this->calculateDefaultTimes($businessHours);
        }
        
        // デバッグ情報
        \Log::info('Admin index method debug', [
            'businessHours' => $businessHours,
            'parsedBusinessHours' => $parsedBusinessHours
        ]);
            
        return view('admin.reservations.index', compact('cars', 'reservations', 'year', 'month', 'businessHours', 'parsedBusinessHours'));
    }

    public function create(Request $request)
    {
        $carId = $request->get('car_id');
        
        if ($carId) {
            $car = Car::with('carModel')->findOrFail($carId);
        } else {
            // 車両IDが指定されていない場合は、最初の車両を取得
            $car = Car::with('carModel')->first();
            
            if (!$car) {
                return redirect()->route('admin.cars.index')
                    ->with('error', '予約を作成するには、まず車両を登録してください。');
            }
        }
        
        // 営業時間を取得（User側と同じロジック）
        $shop = \App\Models\Shop::first();
        $businessHours = $shop ? $shop->business_hours : null;
        $defaultTimes = $this->calculateDefaultTimes($businessHours);
        
        // 営業時間を解析（User側と同じロジック）
        $parsedBusinessHours = null;
        if ($businessHours) {
            $parsedBusinessHours = $this->calculateDefaultTimes($businessHours);
        }
        
        // デバッグ情報
        \Log::info('Admin create method debug', [
            'businessHours' => $businessHours,
            'parsedBusinessHours' => $parsedBusinessHours,
            'defaultTimes' => $defaultTimes
        ]);
        
        return view('admin.reservations.create', compact('car', 'defaultTimes', 'businessHours', 'parsedBusinessHours'));
    }

    public function store(Request $request)
    {
        // デバッグ情報をログに出力
        Log::info('予約作成リクエスト受信', [
            'all_data' => $request->all(),
            'car_id' => $request->get('car_id'),
            'name_kanji' => $request->get('name_kanji'),
            'start_datetime' => $request->get('start_datetime'),
            'end_datetime' => $request->get('end_datetime'),
        ]);

        try {
            $validated = $request->validate([
                'car_id' => 'required|exists:cars,id',
                'name_kanji' => 'required|string|max:255',
                'name_kana_sei' => 'required|string|max:255',
                'name_kana_mei' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone_main' => 'required|string|max:20',
                'phone_emergency' => 'nullable|string|max:20',
                'start_datetime' => 'required|date',
                'end_datetime' => 'required|date|after:start_datetime',
                'number_of_adults' => 'required|integer|min:1|max:10',
                'number_of_children' => 'required|integer|min:0|max:10',
                'flight_number_arrival' => 'nullable|string|max:50',
                'flight_number_departure' => 'nullable|string|max:50',
                'flight_departure' => 'nullable|string|max:100',
                'flight_return' => 'nullable|string|max:100',
                'total_price' => 'required|numeric|min:0',
                'notes' => 'nullable|string|max:1000',
                'status' => 'required|in:pending,confirmed,cancelled',
            ]);
            
            // 営業時間のバリデーション（一時的に無効化）
            /*
            $shop = \App\Models\Shop::first();
            if ($shop && $shop->business_hours) {
                $defaultTimes = $this->calculateDefaultTimes($shop->business_hours);
                $startTime = Carbon::parse($validated['start_datetime']);
                $endTime = Carbon::parse($validated['end_datetime']);
                
                // 営業時間を解析
                $pattern = '/(\d{1,2}):(\d{2})[〜\-~]\s*(\d{1,2}):(\d{2})/u';
                if (preg_match($pattern, $shop->business_hours, $matches)) {
                    $openHour = (int)$matches[1];
                    $openMinute = (int)$matches[2];
                    $closeHour = (int)$matches[3];
                    $closeMinute = (int)$matches[4];
                    
                    // 最終送迎時間を取得（ある場合）
                    $finalPickupPattern = '/\(最終送迎(\d{1,2}):(\d{2})\)/u';
                    $finalPickupHour = $closeHour;
                    $finalPickupMinute = $closeMinute;
                    if (preg_match($finalPickupPattern, $shop->business_hours, $finalMatches)) {
                        $finalPickupHour = (int)$finalMatches[1];
                        $finalPickupMinute = (int)$finalMatches[2];
                    }
                    
                    $startHour = $startTime->hour;
                    $startMinute = $startTime->minute;
                    $endHour = $endTime->hour;
                    $endMinute = $endTime->minute;
                    
                    $startTimeInMinutes = $startHour * 60 + $startMinute;
                    $endTimeInMinutes = $endHour * 60 + $endMinute;
                    $openTimeInMinutes = $openHour * 60 + $openMinute;
                    $closeTimeInMinutes = $closeHour * 60 + $closeMinute;
                    
                    if ($startTimeInMinutes < $openTimeInMinutes || $endTimeInMinutes > $closeTimeInMinutes) {
                        return back()->withErrors([
                            'business_hours' => "営業時間外の時間が選択されています。営業時間: {$openHour}:{$openMinute}〜{$closeHour}:{$closeMinute}"
                        ])->withInput();
                    }
                }
            }
            */
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('バリデーションエラー', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e;
        }

        \Log::info('バリデーション成功', $validated);

        // 車両の重複予約チェック
        $car = Car::findOrFail($validated['car_id']);
        $startDate = Carbon::parse($validated['start_datetime']);
        $endDate = Carbon::parse($validated['end_datetime']);

        $conflictingReservation = Reservation::where('car_id', $validated['car_id'])
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->where('start_datetime', '<', $endDate)
                      ->where('end_datetime', '>', $startDate);
                });
            })
            ->first();

        if ($conflictingReservation) {
            return back()->withErrors(['date_conflict' => '指定された期間は既に予約されています。'])->withInput();
        }

        try {
            DB::beginTransaction();

            // ユーザーを作成または取得
            $user = User::firstOrCreate(
                ['email' => $validated['email']],
                [
                    'name' => $validated['name_kanji'],
                    'phone_number' => $validated['phone_main'],
                    'password' => bcrypt('password123'), // デフォルトパスワードを設定
                ]
            );

            Log::info('ユーザー処理完了', ['user_id' => $user->id]);

            // 予約を作成
            $reservation = Reservation::create([
                'user_id' => $user->id,
                'car_id' => $validated['car_id'],
                'name_kanji' => $validated['name_kanji'],
                'name_kana_sei' => $validated['name_kana_sei'],
                'name_kana_mei' => $validated['name_kana_mei'],
                'email' => $validated['email'],
                'phone_main' => $validated['phone_main'],
                'phone_emergency' => $validated['phone_emergency'] ?? null,
                'start_datetime' => $validated['start_datetime'],
                'end_datetime' => $validated['end_datetime'],
                'number_of_adults' => $validated['number_of_adults'],
                'number_of_children' => $validated['number_of_children'],
                'flight_number_arrival' => $validated['flight_number_arrival'] ?? null,
                'flight_number_departure' => $validated['flight_number_departure'] ?? null,
                'flight_departure' => $validated['flight_departure'] ?? null,
                'flight_return' => $validated['flight_return'] ?? null,
                'total_price' => $validated['total_price'],
                'notes' => $validated['notes'] ?? null,
                'status' => $validated['status'],
            ]);

            Log::info('予約作成完了', ['reservation_id' => $reservation->id]);

            DB::commit();

            Log::info('予約作成成功', [
                'reservation_id' => $reservation->id,
                'car_id' => $reservation->car_id,
                'customer' => $reservation->name_kanji,
                'period' => $reservation->start_datetime->format('Y-m-d H:i') . ' 〜 ' . $reservation->end_datetime->format('Y-m-d H:i')
            ]);

            return redirect()->route('admin.reservations.index', [
                'year' => Carbon::parse($validated['start_datetime'])->year,
                'month' => Carbon::parse($validated['start_datetime'])->month
            ])->with('success', '予約を追加しました');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('予約作成エラー', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'validated_data' => $validated
            ]);
            return back()->withErrors(['error' => '予約の追加に失敗しました: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['car.carModel', 'car.images', 'user', 'options']);
        return view('admin.reservations.show', compact('reservation'));
    }

    public function edit(Reservation $reservation)
    {
        return view('admin.reservations.edit', compact('reservation'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'car_id' => 'required|exists:cars,id',
            'name_kanji' => 'required|string|max:255',
            'name_kana_sei' => 'required|string|max:255',
            'name_kana_mei' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_main' => 'required|string|max:20',
            'phone_emergency' => 'nullable|string|max:20',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'number_of_adults' => 'required|integer|min:1',
            'number_of_children' => 'nullable|integer|min:0',
            'total_price' => 'required|numeric|min:0',
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'notes' => 'nullable|string|max:1000',
        ]);

        // 日時をCarbonオブジェクトに変換
        $validated['start_datetime'] = Carbon::parse($validated['start_datetime']);
        $validated['end_datetime'] = Carbon::parse($validated['end_datetime']);

        // 重複チェック（自分自身を除く）
        $conflictingReservation = Reservation::where('car_id', $validated['car_id'])
            ->where('id', '!=', $reservation->id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_datetime', [$validated['start_datetime'], $validated['end_datetime']])
                    ->orWhereBetween('end_datetime', [$validated['start_datetime'], $validated['end_datetime']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('start_datetime', '<=', $validated['start_datetime'])
                            ->where('end_datetime', '>=', $validated['end_datetime']);
                    });
            })->first();

        if ($conflictingReservation) {
            return back()->withErrors(['date_conflict' => '指定された期間は既に予約されています。'])->withInput();
        }

        $reservation->update($validated);

        return redirect()->route('admin.reservations.show', $reservation)
            ->with('success', '予約情報を更新しました');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return redirect()->route('admin.reservations.index')
            ->with('success', '予約を削除しました');
    }
} 