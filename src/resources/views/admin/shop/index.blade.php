<x-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('店舗情報') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
            <div class="bg-white p-8 rounded shadow">
                
                <!-- 編集ボタン（上に配置） -->
                <div class="flex justify-end mb-6">
                    <a href="{{ route('admin.shop.edit') }}"
                    class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-lg font-semibold text-base text-white uppercase tracking-widest hover:bg-gray-700 transition">
                        編集
                    </a>
                </div>

                <!-- 店舗情報テーブル -->
                <table class="table-auto w-full mb-8 border border-gray-200 shadow-lg">
                    <tbody>
                        <tr class="border-b">
                            <th class="text-left px-4 py-4 w-32">店舗名</th>
                            <td class="px-4 py-4">{{ $shop->name }}</td>
                        </tr>
                        <tr class="border-b">
                            <th class="text-left px-4 py-4">TEL</th>
                            <td class="px-4 py-4">
                                <a href="tel:{{ $shop->tel }}" class="text-blue-600 hover:underline">{{ $shop->tel }}</a>
                            </td>
                        </tr>
                        <tr class="border-b">
                            <th class="text-left px-4 py-4">メールアドレス</th>
                            <td class="px-4 py-4">
                                <a href="mailto:{{ $shop->email }}" class="text-blue-600 hover:underline">{{ $shop->email }}</a>
                            </td>
                        </tr>
                        <tr class="border-b">
                            <th class="text-left px-4 py-4">住所</th>
                            <td class="px-4 py-4">{{ $shop->address }}</td>
                        </tr>
                        <tr class="border-b">
                            <th class="text-left px-4 py-4">営業時間</th>
                            <td class="px-4 py-4">{{ $shop->business_hours }}</td>
                        </tr>
                        <tr>
                            <th class="text-left px-4 py-4">アクセス</th>
                            <td class="px-4 py-4 whitespace-pre-line">{{ $shop->access }}</td>
                        </tr>
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

            </div>
        </div>
    </div>
</x-admin-layout>
