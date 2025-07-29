<div class="relative">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider sticky top-0 z-10">
                <tr>
                    <th class="px-4 py-3 text-left bg-gray-50 sticky left-0 z-20">車種</th>
                    <th class="px-4 py-3 text-center min-w-[120px]">3時間</th>
                    <th class="px-4 py-3 text-center min-w-[120px]">営業時間内<br>(9:00-19:00)</th>
                    <th class="px-4 py-3 text-center min-w-[120px]">24時間</th>
                    <th class="px-4 py-3 text-center min-w-[120px]">48時間</th>
                    <th class="px-4 py-3 text-center min-w-[120px]">72時間</th>
                    <th class="px-4 py-3 text-center min-w-[120px]">1週間</th>
                    <th class="px-4 py-3 text-center min-w-[120px]">延長（1時間）</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                @foreach($carPrices as $price)
                    <tr>
                        <td class="px-4 py-2 bg-gray-50 sticky left-0">
                            <div class="font-medium text-gray-900">{{ $price->carModel->name }}</div>
                            <div class="text-gray-500">{{ $price->carModel->grade }}</div>
                        </td>
                        <td class="px-4 py-2 text-center">¥{{ number_format($price->getPriceWithSeason(3, $rate)) }}</td>
                        <td class="px-4 py-2 text-center">¥{{ number_format($price->getPriceWithSeason(10, $rate, true)) }}</td>
                        <td class="px-4 py-2 text-center">¥{{ number_format($price->getPriceWithSeason(24, $rate)) }}</td>
                        <td class="px-4 py-2 text-center">¥{{ number_format($price->getPriceWithSeason(48, $rate)) }}</td>
                        <td class="px-4 py-2 text-center">¥{{ number_format($price->getPriceWithSeason(72, $rate)) }}</td>
                        <td class="px-4 py-2 text-center">¥{{ number_format($price->getPriceWithSeason(168, $rate)) }}</td>
                        <td class="px-4 py-2 text-center">¥{{ number_format($price->price_extra_hour) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    .sticky {
        position: -webkit-sticky;
        position: sticky;
    }
</style> 