<x-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('車種別料金設定の編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.price.car.update', $price) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">基本情報</h3>
                                <div class="grid grid-cols-1 gap-6">
                                    <div>
                                        <label for="car_model_id" class="block text-sm font-medium text-gray-700">車種</label>
                                        <select name="car_model_id" id="car_model_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            required>
                                            <option value="">選択してください</option>
                                            @foreach($carModels as $model)
                                                <option value="{{ $model->id }}" {{ old('car_model_id', $price->car_model_id) == $model->id ? 'selected' : '' }}>
                                                    {{ $model->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">料金設定</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="price_6h" class="block text-sm font-medium text-gray-700">6時間料金</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <input type="number" name="price_6h" id="price_6h" value="{{ old('price_6h', $price->price_6h) }}"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                min="0" required>
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">円</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="price_12h" class="block text-sm font-medium text-gray-700">12時間料金</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <input type="number" name="price_12h" id="price_12h" value="{{ old('price_12h', $price->price_12h) }}"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                min="0" required>
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">円</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="price_24h" class="block text-sm font-medium text-gray-700">24時間料金</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <input type="number" name="price_24h" id="price_24h" value="{{ old('price_24h', $price->price_24h) }}"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                min="0" required>
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">円</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="price_48h" class="block text-sm font-medium text-gray-700">48時間料金</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <input type="number" name="price_48h" id="price_48h" value="{{ old('price_48h', $price->price_48h) }}"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                min="0" required>
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">円</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="price_72h" class="block text-sm font-medium text-gray-700">72時間料金</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <input type="number" name="price_72h" id="price_72h" value="{{ old('price_72h', $price->price_72h) }}"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                min="0" required>
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">円</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="price_168h" class="block text-sm font-medium text-gray-700">1週間料金</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <input type="number" name="price_168h" id="price_168h" value="{{ old('price_168h', $price->price_168h) }}"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                min="0" required>
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">円</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="price_extra_hour" class="block text-sm font-medium text-gray-700">延長料金（1時間）</label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <input type="number" name="price_extra_hour" id="price_extra_hour" value="{{ old('price_extra_hour', $price->price_extra_hour) }}"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                min="0" required>
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">円</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-4">
                                <button type="button" onclick="location.href='{{ route('admin.price.index') }}'"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    キャンセル
                                </button>
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    更新する
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout> 