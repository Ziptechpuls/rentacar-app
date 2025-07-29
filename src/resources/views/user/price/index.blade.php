<x-user-layout>
    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- 現在のシーズン情報 --}}
            <div class="mb-8 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">現在の料金シーズン</h2>
                @if($currentSeason)
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    現在は<span class="font-bold">{{ $currentSeason->name }}</span>期間です（料金{{ $rate < 100 ? 'より'.$rate.'%お得' : $rate.'%増' }}）
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-gray-600">現在は通常料金期間です</p>
                @endif
            </div>

            {{-- 料金表 --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">料金表</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">車種</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">6時間</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">12時間</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">24時間</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">48時間</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">72時間</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">1週間</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($carPrices as $price)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $price->carModel->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $price->carModel->grade }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="text-sm text-gray-900">¥{{ number_format($price->getPriceWithSeason(6, $rate)) }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="text-sm text-gray-900">¥{{ number_format($price->getPriceWithSeason(12, $rate)) }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="text-sm text-gray-900">¥{{ number_format($price->getPriceWithSeason(24, $rate)) }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="text-sm text-gray-900">¥{{ number_format($price->getPriceWithSeason(48, $rate)) }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="text-sm text-gray-900">¥{{ number_format($price->getPriceWithSeason(72, $rate)) }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="text-sm text-gray-900">¥{{ number_format($price->getPriceWithSeason(168, $rate)) }}</div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-sm text-gray-500">
                    <p>※ 表示価格は税込です。</p>
                    <p>※ 延長料金：1時間あたり基本料金の10%</p>
                </div>
            </div>

            {{-- 今後のシーズン情報 --}}
            @if($futureSeasons->isNotEmpty())
                <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">今後のシーズン情報</h2>
                    <div class="space-y-4">
                        @foreach($futureSeasons as $season)
                            <div class="border-l-4 {{ $season->rate < 100 ? 'border-green-400 bg-green-50' : 'border-yellow-400 bg-yellow-50' }} p-4">
                                <h3 class="text-lg font-semibold">{{ $season->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $season->description }}</p>
                                <div class="mt-2 text-sm">
                                    @foreach($season->periods as $period)
                                        <div class="text-gray-700">
                                            {{ Carbon\Carbon::parse($period->start_date)->format('Y年n月j日') }} 〜
                                            {{ Carbon\Carbon::parse($period->end_date)->format('Y年n月j日') }}
                                        </div>
                                    @endforeach
                                </div>
                                <p class="mt-2 text-sm font-medium {{ $season->rate < 100 ? 'text-green-700' : 'text-yellow-700' }}">
                                    通常料金{{ $season->rate < 100 ? 'より'.(100 - $season->rate).'%お得' : 'の'.($season->rate - 100).'%増' }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-user-layout> 