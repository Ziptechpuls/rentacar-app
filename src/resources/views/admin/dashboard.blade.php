<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ダッシュボード') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 統計情報カード -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- 総予約数 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">総予約数</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $totalReservations ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 今月の売上 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">今月の売上</p>
                                <p class="text-2xl font-semibold text-gray-900">¥{{ number_format($monthlyRevenue ?? 0) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 利用可能車両数 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">利用可能車両</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $availableCars ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- アクティブユーザー数 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">アクティブユーザー</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $activeUsers ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- 今日の予約と返却 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- 今日の予約（出発） -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            今日の予約（出発）
                        </h3>
                        @if($todayDepartures->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">顧客名</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">車両</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">出発時間</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($todayDepartures as $reservation)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $reservation->name_kanji ?? $reservation->user->name ?? 'N/A' }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $reservation->car->carModel->name ?? 'N/A' }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                    {{ \Carbon\Carbon::parse($reservation->start_datetime)->format('H:i') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500">今日の出発予約はありません。</p>
                        @endif
                    </div>
                </div>

                <!-- 今日の返却 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            今日の返却
                        </h3>
                        @if($todayReturns->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">顧客名</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">車両</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">返却時間</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($todayReturns as $reservation)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $reservation->name_kanji ?? $reservation->user->name ?? 'N/A' }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $reservation->car->carModel->name ?? 'N/A' }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                    {{ \Carbon\Carbon::parse($reservation->end_datetime)->format('H:i') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500">今日の返却予約はありません。</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 車種別売上 -->
            @if(isset($carModelSales) && $carModelSales->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-8">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">車種別売上ランキング（今月）</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">順位</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">車種</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">売上</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">割合</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($carModelSales->take(10) as $index => $carModel)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <div class="flex items-center">
                                                @if($index === 0)
                                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold text-white shadow-sm bg-yellow-500">
                                                        {{ $index + 1 }}
                                                    </span>
                                                @elseif($index === 1)
                                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold text-white shadow-sm bg-gray-500">
                                                        {{ $index + 1 }}
                                                    </span>
                                                @elseif($index === 2)
                                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold text-white shadow-sm bg-red-500">
                                                        {{ $index + 1 }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold text-gray-600 bg-gray-200 shadow-sm">
                                                        {{ $index + 1 }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $carModel['car_model'] }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            ¥{{ number_format($carModel['total_sales']) }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            {{ $carModel['percentage'] }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-admin-layout>
