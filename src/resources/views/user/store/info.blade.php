<x-user-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('店舗情報') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
            <div class="bg-white p-8">
                @if($shop)
                    <!-- 店舗情報テーブル -->
                    <table class="table-auto w-full mb-8 border-gray-100 shadow-lg border">
                        <tbody>
                            <tr class="border-b-2">
                                <th class="text-left px-4 py-4 w-32">店舗名</th>
                                <td class="px-4 py-4">{{ $shop->name }}</td>
                            </tr>
                            <tr class="border-b-2">
                                <th class="text-left px-4 py-4">TEL</th>
                                <td class="px-4 py-4">
                                    <a href="tel:{{ $shop->tel }}" class="text-blue-600 hover:underline">{{ $shop->tel }}</a>
                                </td>
                            </tr>
                            <tr class="border-b-2">
                                <th class="text-left px-4 py-4">メールアドレス</th>
                                <td class="px-4 py-4">
                                    <a href="mailto:{{ $shop->email }}" class="text-blue-600 hover:underline">{{ $shop->email }}</a>
                                </td>
                            </tr>
                            <tr class="border-b-2">
                                <th class="text-left px-4 py-4">住所</th>
                                <td class="px-4 py-4">{{ $shop->address }}</td>
                            </tr>
                            <tr class="border-b-2">
                                <th class="text-left px-4 py-4 w-32">営業時間</th>
                                <td class="px-4 py-4">{{ $shop->business_hours }}</td>
                            </tr>
                            @if($shop->access)
                                <tr>
                                    <th class="text-left px-4 py-4">アクセス</th>
                                    <td class="px-4 py-4 whitespace-pre-line">{{ $shop->access }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                    @if($shop->map_iframe)
                        <!-- Google Maps埋め込み -->
                        <div class="space-y-6">
                            <div class="w-full border rounded-lg overflow-hidden" style="height: 400px;">
                                {!! $shop->map_iframe !!}
                            </div>

                            <div class="text-center">
                                <a href="https://www.google.com/maps?q={{ urlencode($shop->address) }}"
                                target="_blank"
                                class="w-full inline-block font-semibold py-2 px-6 rounded bg-gray-800 text-white hover:bg-gray-700">
                                    Google Mapsで見る
                                </a>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">店舗情報がありません</h3>
                        <p class="mt-1 text-sm text-gray-500">店舗情報は準備中です。しばらくお待ちください。</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-user-layout>