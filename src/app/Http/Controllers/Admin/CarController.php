<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\CarTypePrice;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 25);
        $cars = Car::with(['carModel', 'images'])->latest()->paginate($perPage);
        return view('admin.cars.index', compact('cars'));
    }

    public function show(Car $car, Request $request)
    {
        // 表示する月を取得（デフォルトは現在月）
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        
        // 指定月の開始日と終了日を計算
        $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        
        // 前月と翌月のリンク用
        $prevMonth = $startDate->copy()->subMonth();
        $nextMonth = $startDate->copy()->addMonth();
        
        // 車両の予約状況を取得（指定月の前後1週間も含める）
        $queryStartDate = $startDate->copy()->subWeek();
        $queryEndDate = $endDate->copy()->addWeek();
        
        $reservations = $car->reservations()
            ->where(function($query) use ($queryStartDate, $queryEndDate) {
                $query->whereBetween('start_datetime', [$queryStartDate, $queryEndDate])
                      ->orWhereBetween('end_datetime', [$queryStartDate, $queryEndDate])
                      ->orWhere(function($subQuery) use ($queryStartDate, $queryEndDate) {
                          $subQuery->where('start_datetime', '<=', $queryStartDate)
                                   ->where('end_datetime', '>=', $queryEndDate);
                      });
            })
            ->with('user')
            ->orderBy('start_datetime')
            ->get();

        // 予約状況を日付別に整理
        $reservationCalendar = [];
        foreach ($reservations as $reservation) {
            if ($reservation->start_datetime && $reservation->end_datetime) {
                $currentDate = $reservation->start_datetime->copy();
                while ($currentDate <= $reservation->end_datetime) {
                    $dateKey = $currentDate->format('Y-m-d');
                    if (!isset($reservationCalendar[$dateKey])) {
                        $reservationCalendar[$dateKey] = [];
                    }
                    $reservationCalendar[$dateKey][] = $reservation;
                    $currentDate->addDay();
                }
            }
        }

        return view('admin.cars.show', compact(
            'car', 
            'reservations', 
            'reservationCalendar', 
            'startDate', 
            'endDate', 
            'prevMonth', 
            'nextMonth',
            'year',
            'month'
        ));
    }

    // 車両の登録画面を表示
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    // 車両タイプに対応する料金設定が存在するかチェック
                    $carTypePrice = \App\Models\CarTypePrice::getApplicablePriceByTypeAndDate($value, now());
                    if (!$carTypePrice) {
                        $fail("選択された車両タイプ「{$value}」の料金設定が見つかりません。先に車両タイプ別料金設定で料金を設定してください。");
                    }
                }
            ],
            'color' => 'nullable|string|max:50',
            'inspection_date' => 'nullable|date',
            'capacity' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'transmission' => 'required|string|in:AT,MT',
            'smoking_preference' => ['required', Rule::in(['禁煙', '喫煙可', '電子タバコのみ可'])],
            'has_bluetooth' => 'nullable|boolean',
            'has_back_monitor' => 'nullable|boolean',
            'has_navigation' => 'nullable|boolean',
            'has_etc' => 'nullable|boolean',
            'description' => 'nullable|string',
            'images' => 'nullable|array|max:5', // 最大5枚まで
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // 各画像は2MBまで
            'is_public' => 'required|boolean',
            'car_model_id' => 'nullable|exists:car_models,id', // もしCarModelを使用する場合
            'license_plate' => 'nullable|string|unique:cars,license_plate', // もしナンバープレートを登録する場合
            'store_id' => 'nullable|integer|exists:stores,id', // もし店舗情報と紐付ける場合        
        ]);
    
        // チェックボックスの値をbooleanに変換
        $validated['has_bluetooth'] = $request->boolean('has_bluetooth');
        $validated['has_back_monitor'] = $request->boolean('has_back_monitor');
        $validated['has_navigation'] = $request->boolean('has_navigation');
        $validated['has_etc'] = $request->boolean('has_etc');

        // ログイン中の管理者の会社IDを設定
        $validated['company_id'] = auth('admin')->user()->company_id;

        // 車両タイプに応じて料金を自動設定
        // バリデーションで料金設定の存在を確認済みなので、必ず取得できる
        $carTypePrice = \App\Models\CarTypePrice::getApplicablePriceByTypeAndDate($validated['type'], now());
        $validated['price'] = $carTypePrice->price_per_day;

        // 画像以外のデータを先に保存
        $carData = collect($validated)->except('images')->toArray();
        $car = Car::create($carData);

        // 画像アップロード処理
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                // ファイル名にタイムスタンプを付加してユニークにするなどの工夫も可能
                $path = $imageFile->store('cars', 'public'); // storage/app/public/cars ディレクトリに保存
                $car->images()->create(['image_path' => $path]);
            }
        }    
        return redirect()->route('admin.cars.index')->with('success', '車両を登録しました');
    }

    // 車両の編集画面を表示
    public function edit(Car $car)
    {
        $carModels = CarModel::all(); // CarModelを使用しない場合は不要
        return view('admin.cars.edit', compact('car', 'carModels'));
    }

    public function create()
    {
        $cars = Car::latest()->get(); // 一覧を取得
        return view('admin.cars.create', compact('cars'));    }

    // 車両の更新処理
    public function update(Request $request, Car $car)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    // 車両タイプに対応する料金設定が存在するかチェック
                    $carTypePrice = \App\Models\CarTypePrice::getApplicablePriceByTypeAndDate($value, now());
                    if (!$carTypePrice) {
                        $fail("選択された車両タイプ「{$value}」の料金設定が見つかりません。先に車両タイプ別料金設定で料金を設定してください。");
                    }
                }
            ],
            'color' => 'nullable|string|max:50',
            'capacity' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'transmission' => 'required|string|in:AT,MT',
            'smoking_preference' => ['required', Rule::in(['禁煙', '喫煙可', '電子タバコのみ可'])],
            'has_bluetooth' => 'nullable|boolean',
            'has_back_monitor' => 'nullable|boolean',
            'has_navigation' => 'nullable|boolean',
            'has_etc' => 'nullable|boolean',
            'description' => 'nullable|string',
            'images' => 'nullable|array|max:5',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_public' => 'required|boolean',
            'car_model_id' => 'nullable|exists:car_models,id',
            'license_plate' => 'nullable|string|max:255',
            'vin_number' => 'nullable|string|max:255',
            'inspection_date' => 'nullable|date',
            'store_id' => 'nullable|integer|exists:stores,id',        
        ]);

        // チェックボックスの値をbooleanに変換
        $validated['has_bluetooth'] = $request->boolean('has_bluetooth');
        $validated['has_back_monitor'] = $request->boolean('has_back_monitor');
        $validated['has_navigation'] = $request->boolean('has_navigation');
        $validated['has_etc'] = $request->boolean('has_etc');

        // 車両タイプに応じて料金を自動設定（更新時も適用）
        // バリデーションで料金設定の存在を確認済みなので、必ず取得できる
        $carTypePrice = \App\Models\CarTypePrice::getApplicablePriceByTypeAndDate($validated['type'], now());
        $validated['price'] = $carTypePrice->price_per_day;

        // 画像以外のデータを先に更新
        $carData = collect($validated)->except('images')->toArray();
        $car->update($carData);

        // 画像アップロード処理 (既存の画像を削除し、新しい画像を登録するなどのロジックが必要になる場合がある)
        // ここでは単純に追加する例を示しますが、実際の要件に合わせて調整してください。
        // 例えば、既存の画像を削除するチェックボックスをフォームに設け、選択されたら削除するなど。
        if ($request->hasFile('images')) {
            // 必要であれば既存の画像を削除する処理をここに追加
            // $car->images()->delete(); // 例: 全削除する場合
            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store('cars', 'public');
                $car->images()->create(['image_path' => $path]);
            }
        }
        return redirect()->route('admin.cars.index')->with('success', '車両情報を更新しました');
    }

    // 車両の削除処理
    public function destroy(Car $car)
    {
        // 関連する画像をストレージから削除し、DBからも削除
        foreach ($car->images as $image) {
            // Storageファサードを使用するために use Illuminate\Support\Facades\Storage; を追加
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
        }

        // DBから削除
        $car->delete();

        return redirect()->route('admin.cars.index')->with('success', '車両を削除しました');
    }

    // 車両の公開・非公開を切り替える
    public function togglePublish(Car $car)
    {
        $car->is_public = !$car->is_public;
        $car->save();

        return response()->json([
            'success' => true,
            'status' => $car->is_public ? 'public' : 'private',
            'message' => $car->is_public ? '車両を公開しました' : '車両を非公開にしました'
        ]);
    }
}
