<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriceSeason;
use App\Models\CarPrice;
use App\Models\CarModel;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    public function index()
    {
        $seasons = PriceSeason::with('periods')->get();
        $carPrices = CarPrice::with('carModel')->get();

        return view('admin.price.index', compact('seasons', 'carPrices'));
    }

    public function seasonCreate()
    {
        return view('admin.price.season.create');
    }

    public function seasonStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'rate' => 'required|integer|min:1|max:200',
            'periods' => 'required|array|min:1',
            'periods.*.start_date' => 'required|date',
            'periods.*.end_date' => 'required|date|after:periods.*.start_date',
        ]);

        $season = PriceSeason::create([
            'name' => $request->name,
            'description' => $request->description,
            'rate' => $request->rate,
        ]);

        foreach ($request->periods as $period) {
            $season->periods()->create([
                'start_date' => $period['start_date'],
                'end_date' => $period['end_date'],
            ]);
        }

        return redirect()->route('admin.price.index')
            ->with('success', 'シーズンを追加しました。');
    }

    public function seasonEdit(PriceSeason $season)
    {
        $season->load('periods');
        return view('admin.price.season.edit', compact('season'));
    }

    public function seasonUpdate(Request $request, PriceSeason $season)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'rate' => 'required|integer|min:1|max:200',
            'periods' => 'required|array|min:1',
            'periods.*.start_date' => 'required|date',
            'periods.*.end_date' => 'required|date|after:periods.*.start_date',
        ]);

        $season->update([
            'name' => $request->name,
            'description' => $request->description,
            'rate' => $request->rate,
        ]);

        // 既存の期間を削除して新しい期間を作成
        $season->periods()->delete();
        foreach ($request->periods as $period) {
            $season->periods()->create([
                'start_date' => $period['start_date'],
                'end_date' => $period['end_date'],
            ]);
        }

        return redirect()->route('admin.price.index')
            ->with('success', 'シーズンを更新しました。');
    }

    public function seasonDestroy(PriceSeason $season)
    {
        $season->delete();
        return redirect()->route('admin.price.index')
            ->with('success', 'シーズンを削除しました。');
    }

    public function carCreate()
    {
        $carModels = CarModel::all();
        return view('admin.price.car.create', compact('carModels'));
    }

    public function carStore(Request $request)
    {
        $request->validate([
            'car_model_id' => 'required|exists:car_models,id',
            'price_6h' => 'required|integer|min:0',
            'price_12h' => 'required|integer|min:0',
            'price_24h' => 'required|integer|min:0',
            'price_48h' => 'required|integer|min:0',
            'price_72h' => 'required|integer|min:0',
            'price_168h' => 'required|integer|min:0',
            'price_extra_hour' => 'required|integer|min:0',
        ]);

        CarPrice::create($request->validated());

        return redirect()->route('admin.price.index')
            ->with('success', '料金設定を追加しました。');
    }

    public function carEdit(CarPrice $price)
    {
        $carModels = CarModel::all();
        return view('admin.price.car.edit', compact('price', 'carModels'));
    }

    public function carUpdate(Request $request, CarPrice $price)
    {
        $request->validate([
            'car_model_id' => 'required|exists:car_models,id',
            'price_6h' => 'required|integer|min:0',
            'price_12h' => 'required|integer|min:0',
            'price_24h' => 'required|integer|min:0',
            'price_48h' => 'required|integer|min:0',
            'price_72h' => 'required|integer|min:0',
            'price_168h' => 'required|integer|min:0',
            'price_extra_hour' => 'required|integer|min:0',
        ]);

        $price->update($request->validated());

        return redirect()->route('admin.price.index')
            ->with('success', '料金設定を更新しました。');
    }

    public function carDestroy(CarPrice $price)
    {
        $price->delete();
        return redirect()->route('admin.price.index')
            ->with('success', '料金設定を削除しました。');
    }
}
