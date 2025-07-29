<x-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('料金管理') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- シーズン設定セクション --}}
            <div class="mb-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">シーズン設定</h3>
                        <button onclick="location.href='{{ route('admin.price.season.create') }}'"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            新規シーズン追加
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">シーズン名</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">説明</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">料金倍率</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">期間</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($seasons as $season)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $season->name }}</td>
                                    <td class="px-6 py-4">{{ $season->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $season->rate }}%</td>
                                    <td class="px-6 py-4">
                                        @foreach($season->periods as $period)
                                            <div class="mb-1">
                                                {{ \Carbon\Carbon::parse($period->start_date)->format('Y/m/d') }} 〜
                                                {{ \Carbon\Carbon::parse($period->end_date)->format('Y/m/d') }}
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        <button onclick="location.href='{{ route('admin.price.season.edit', $season) }}'"
                                            class="text-indigo-600 hover:text-indigo-900 mr-3">編集</button>
                                        <form action="{{ route('admin.price.season.destroy', $season) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('本当に削除しますか？')">削除</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- 車種別料金設定セクション --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">車種別料金設定</h3>
                        <button onclick="location.href='{{ route('admin.price.car.create') }}'"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            新規料金設定
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">車種</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">6時間</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">12時間</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">24時間</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">48時間</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">72時間</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">1週間</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">延長1時間</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">操作</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($carPrices as $price)
                                <tr>
                                    <td class="px-4 py-4">{{ $price->carModel->name }}</td>
                                    <td class="px-4 py-4 text-center">¥{{ number_format($price->price_6h) }}</td>
                                    <td class="px-4 py-4 text-center">¥{{ number_format($price->price_12h) }}</td>
                                    <td class="px-4 py-4 text-center">¥{{ number_format($price->price_24h) }}</td>
                                    <td class="px-4 py-4 text-center">¥{{ number_format($price->price_48h) }}</td>
                                    <td class="px-4 py-4 text-center">¥{{ number_format($price->price_72h) }}</td>
                                    <td class="px-4 py-4 text-center">¥{{ number_format($price->price_168h) }}</td>
                                    <td class="px-4 py-4 text-center">¥{{ number_format($price->price_extra_hour) }}</td>
                                    <td class="px-4 py-4 text-right text-sm">
                                        <button onclick="location.href='{{ route('admin.price.car.edit', $price) }}'"
                                            class="text-indigo-600 hover:text-indigo-900 mr-3">編集</button>
                                        <form action="{{ route('admin.price.car.destroy', $price) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('本当に削除しますか？')">削除</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

