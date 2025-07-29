<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{Reservation, Car, Option};
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ReservationController extends Controller
{
    private function calculatePrice(Car $car, Carbon $start, Carbon $end, array $selectedOptions): array
    {
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

        $carPrice = $car->price * $days;

        $options = Option::findMany(array_keys($selectedOptions))->keyBy('id');
        $optionTotal = 0;
        $selected = [];

        foreach ($selectedOptions as $id => $val) {
            if (!isset($options[$id]) || !$val) continue;
            $opt = $options[$id];
            $qty = $opt->is_quantity ? (int)$val : 1;
            
            // 料金タイプに応じて計算
            if ($opt->price_type === 'per_piece') {
                // 1個あたりの料金の場合は数量のみ掛ける
                $price = $opt->price * $qty;
            } else {
                // 1日あたりの料金の場合は数量と日数を掛ける
                $price = $opt->price * $qty * $days;
            }
            
            $optionTotal += $price;
            $selected[] = [
                'name' => $opt->name,
                'price' => $price,
                'unit_price' => $opt->price,
                'quantity' => $qty,
                'price_type' => $opt->price_type,
            ];
        }

        return [
            'days' => $days,
            'nights' => $nights,
            'isSameDay' => $isSameDay,
            'carPrice' => $carPrice,
            'optionTotal' => $optionTotal,
            'selectedOptionsDisplay' => $selected,
            'total' => $carPrice + $optionTotal,
        ];
    }

    public function showOptionConfirm(Car $car, Request $request)
    {
        $validated = $request->validate([
            'start_datetime' => ['required', 'date_format:Y-m-d H:i'],
            'end_datetime' => ['required', 'date_format:Y-m-d H:i', 'after:start_datetime'],
            'options' => ['nullable', 'array'],
            'options.*' => ['nullable', 'numeric'],
        ]);

        $start = Carbon::parse($validated['start_datetime']);
        $end = Carbon::parse($validated['end_datetime']);
        $options = $validated['options'] ?? [];

        $priceData = $this->calculatePrice($car, $start, $end, $options);

        return view('user.reservations.option-confirm', array_merge(
            compact('car', 'start', 'end'),
            $priceData,
            [
                'start_datetime_str' => $validated['start_datetime'],
                'end_datetime_str' => $validated['end_datetime'],
                'selected_options_for_post' => $options,
            ]
        ));
    }

    public function carConfirm(Car $car, Request $request)
    {
        $validated = $request->validate([
            'start_datetime' => ['required', 'date_format:Y-m-d H:i'],
            'end_datetime' => ['required', 'date_format:Y-m-d H:i', 'after:start_datetime'],
            'options' => ['nullable', 'array'],
            'options.*' => ['nullable', 'integer', 'min:0'],
        ]);

        session(["reservation.{$car->id}" => $validated]);
        return redirect()->route('user.cars.reservations.input', ['car' => $car->id]);
    }

    public function input(Car $car)
    {
        $data = session("reservation.{$car->id}");
        if (!$data) return redirect()->route('user.cars.show', $car)->withErrors('セッションが切れました');

        $start = Carbon::parse($data['start_datetime']);
        $end = Carbon::parse($data['end_datetime']);
        $options = $data['options'] ?? [];

        $priceData = $this->calculatePrice($car, $start, $end, $options);

        return view('user.reservations.input', array_merge(
            compact('car', 'start', 'end'),
            $priceData,
            [
                'start_datetime_str' => $data['start_datetime'],
                'end_datetime_str' => $data['end_datetime'],
                'selected_options_from_session' => $options,
            ]
        ));
    }

    private function reservationValidationRules(): array
    {
        return [
            'car_id' => ['required', 'integer', 'exists:cars,id'],
            'name_kanji' => ['required', 'string', 'max:255'],
            'name_kana_sei' => ['required', 'string', 'max:255', 'regex:/^[ァ-ヶー]+$/u'],
            'name_kana_mei' => ['required', 'string', 'max:255', 'regex:/^[ァ-ヶー]+$/u'],
            'phone_main' => ['required', 'string', 'max:20'],
            'phone_emergency' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'flight_departure' => ['nullable', 'string', 'max:255'],
            'flight_return' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'start_datetime' => ['required', 'date_format:Y-m-d H:i'],
            'end_datetime' => ['required', 'date_format:Y-m-d H:i', 'after:start_datetime'],
            'options' => ['nullable', 'array'],
            'options.*' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function confirmCustomer(Car $car, Request $request)
    {
        $validated = $request->validate($this->reservationValidationRules());
        if ($car->id != $validated['car_id']) return back()->withErrors('車両が一致しません')->withInput();

        // セッションに保存
        session(["reservation.{$car->id}.customer" => $validated]);

        return redirect()->route('user.cars.reservations.final-confirm', ['car' => $car->id]);
    }

    public function finalConfirm(Car $car, Request $request)
    {
        // セッションから予約情報を取得
        $data = session("reservation.{$car->id}");
        $customerData = session("reservation.{$car->id}.customer");
        if (!$data || !$customerData) {
            return redirect()->route('user.cars.show', $car)->withErrors('セッションが切れました');
        }

        $start = Carbon::parse($data['start_datetime']);
        $end = Carbon::parse($data['end_datetime']);
        $options = $data['options'] ?? [];

        $priceData = $this->calculatePrice($car, $start, $end, $options);
        $customer = collect($customerData)->except(['start_datetime', 'end_datetime', 'options', 'car_id'])->all();

        return view('user.reservations.final-confirm', array_merge(
            compact('car', 'start', 'end', 'customer'),
            $priceData,
            [
                'start_datetime_str' => $data['start_datetime'],
                'end_datetime_str' => $data['end_datetime'],
                'selected_options' => $options,
            ]
        ));
    }

    public function reserved(Car $car, Request $request)
    {
        try {
            // デバッグ: リクエストデータをログに出力
            Log::info('=== Reserved method started ===');
            Log::info('Reserved request data:', $request->all());
            
            $validated = $request->validate([
                'start_datetime' => ['required', 'date_format:Y-m-d H:i'],
                'end_datetime' => ['required', 'date_format:Y-m-d H:i', 'after:start_datetime'],
                'name_kanji' => ['required', 'string', 'max:100'],
                'name_kana_sei' => ['required', 'string', 'max:50'],
                'name_kana_mei' => ['required', 'string', 'max:50'],
                'phone_main' => ['required', 'string', 'max:20'],
                'phone_emergency' => ['nullable', 'string', 'max:20'],
                'email' => ['required', 'email', 'max:255'],
                'flight_departure' => ['nullable', 'string', 'max:100'],
                'flight_return' => ['nullable', 'string', 'max:100'],
                'note' => ['nullable', 'string', 'max:1000'],
                'options' => ['nullable', 'array'],
            ]);

            Log::info('バリデーション成功:', $validated);

        // 重複予約チェック
        Log::info('重複予約チェック開始');
        $startDateTime = Carbon::parse($validated['start_datetime']);
        $endDateTime = Carbon::parse($validated['end_datetime']);
        Log::info('日時解析完了:', ['start' => $startDateTime, 'end' => $endDateTime]);
        
        Log::info('車両空き状況チェック開始');
        if (!Reservation::isCarAvailable($car->id, $startDateTime, $endDateTime)) {
            Log::info('車両が利用不可');
            return back()->withErrors(['start_datetime' => '指定された期間は既に予約されています。別の時間を選択してください。'])->withInput();
        }
        Log::info('車両空き状況チェック完了');

        // 電話番号のフォーマット統一
        if (!empty($validated['phone_main'])) {
            $validated['phone_main'] = str_replace(['-', 'ー', ' '], '', $validated['phone_main']);
        }
        if (!empty($validated['phone_emergency'])) {
            $validated['phone_emergency'] = str_replace(['-', 'ー', ' '], '', $validated['phone_emergency']);
        }

        // 予約データの作成
        $reservationData = [
            'car_id' => $car->id,
            'user_id' => auth()->id(),
            'start_datetime' => $startDateTime,
            'end_datetime' => $endDateTime,
            'name_kanji' => $validated['name_kanji'],
            'name_kana_sei' => $validated['name_kana_sei'],
            'name_kana_mei' => $validated['name_kana_mei'],
            'phone_main' => $validated['phone_main'],
            'phone_emergency' => $validated['phone_emergency'],
            'email' => $validated['email'],
            'flight_departure' => $validated['flight_departure'],
            'flight_return' => $validated['flight_return'],
            'note' => $validated['note'],
            'status' => 'pending',
        ];

        // 料金計算
        $priceData = $this->calculatePrice($car, $startDateTime, $endDateTime, $validated['options'] ?? []);
        $reservationData['total_price'] = $priceData['total'];

        // 予約を作成
        $reservation = Reservation::create($reservationData);

        // オプションを関連付け
        if (!empty($validated['options'])) {
            $optionsData = [];
            foreach ($validated['options'] as $optionId => $quantity) {
                if ($quantity) {
                    $option = Option::find($optionId);
                    if ($option) {
                        $optionPrice = $option->price * $quantity * $priceData['days'];
                        $optionsData[$optionId] = [
                            'quantity' => $quantity,
                            'price' => $option->price,
                            'total_price' => $optionPrice,
                        ];
                    }
                }
            }
            $reservation->options()->attach($optionsData);
        }

        // セッションから予約情報を削除
        session()->forget("reservation.{$car->id}");
        session()->forget("reservation.{$car->id}.customer");

        return redirect()->route('user.cars.reservations.complete', [$car, $reservation]);
        
        } catch (\Exception $e) {
            Log::error('予約処理でエラーが発生:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors('予約処理中にエラーが発生しました: ' . $e->getMessage())->withInput();
        }
    }

    public function complete(Car $car, Reservation $reservation)
    {
        // 予約が該当の車両のものかチェック
        if ($reservation->car_id !== $car->id) {
            return redirect()->route('user.cars.show', $car)->withErrors('無効な予約です');
        }
        return view('user.reservations.complete', compact('reservation'));
    }
}
