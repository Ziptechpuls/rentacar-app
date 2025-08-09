<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('車両タイプ別料金編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if ($errors->any())
                        <div class="mb-4">
                            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.car-type-prices.update', $carTypePrice) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="car_type" class="block text-sm font-medium text-gray-700">車両タイプ <span class="text-red-500">*</span></label>
                                <select id="car_type" name="car_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-200 focus:border-indigo-300">
                                    <option value="">選択してください</option>
                                    @foreach($carTypes as $key => $value)
                                        <option value="{{ $key }}" {{ old('car_type', $carTypePrice->car_type) == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="period_name" class="block text-sm font-medium text-gray-700">期間名</label>
                                <input id="period_name" name="period_name" type="text" value="{{ old('period_name', $carTypePrice->period_name) }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-200 focus:border-indigo-300" 
                                    placeholder="例：通常期、繁忙期、シーズン期など" />
                            </div>

                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700">開始日</label>
                                <input id="start_date" name="start_date" type="date" value="{{ old('start_date', $carTypePrice->start_date ? $carTypePrice->start_date->format('Y-m-d') : '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-200 focus:border-indigo-300" />
                            </div>

                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700">終了日</label>
                                <input id="end_date" name="end_date" type="date" value="{{ old('end_date', $carTypePrice->end_date ? $carTypePrice->end_date->format('Y-m-d') : '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-200 focus:border-indigo-300" />
                            </div>

                                                                                <div>
                                <label for="season_type" class="block text-sm font-medium text-gray-700">適用するシーズン料金 <span class="text-red-500">*</span></label>
                                <select id="season_type" name="season_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-200 focus:border-indigo-300">
                                    <option value="">選択してください</option>
                                    <option value="high_season" {{ old('season_type', $carTypePrice->season_type) == 'high_season' ? 'selected' : '' }}>ハイシーズン</option>
                                    <option value="normal" {{ old('season_type', $carTypePrice->season_type) == 'normal' ? 'selected' : '' }}>通常</option>
                                    <option value="low_season" {{ old('season_type', $carTypePrice->season_type) == 'low_season' ? 'selected' : '' }}>ローシーズン</option>
                                </select>
                            </div>

                            <div>
                                <label for="price_per_day" class="block text-sm font-medium text-gray-700">1日あたりの料金 <span class="text-red-500">*</span></label>
                                <input id="price_per_day" name="price_per_day" type="number" value="{{ old('price_per_day', $carTypePrice->price_per_day) }}" 
                                    required min="1" max="999999"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-200 focus:border-indigo-300" 
                                    placeholder="例：6000" />
                                <p class="mt-1 text-xs text-gray-500">必ず入力してください（1円以上）</p>
                            </div>

                            <div class="md:col-span-2">
                                <label for="notes" class="block text-sm font-medium text-gray-700">備考</label>
                                <textarea id="notes" name="notes" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-200 focus:border-indigo-300"
                                    placeholder="料金設定に関する備考があれば記入してください">{{ old('notes', $carTypePrice->notes) }}</textarea>
                            </div>

                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $carTypePrice->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-200">
                                    <span class="ml-2 text-sm text-gray-900">有効にする</span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end space-x-3">
                            <a href="{{ route('admin.car-type-prices.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                キャンセル
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                更新
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout> 