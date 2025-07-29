<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('顧客管理') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                        {{-- 検索フォーム --}}
                        <form method="GET" action="{{ route('admin.customers.index') }}" class="mb-6">
                            {{-- 注意書き --}}
                            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800">検索のヒント</h3>
                                        <div class="mt-1 text-sm text-blue-700">
                                            <p>今日の出発を検索したい場合は、利用開始日と利用終了日を同じ日付で入力してください。</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap gap-4 items-end">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">検索</label>
                                    <input
                                        type="text"
                                        name="search"
                                        placeholder="名前または電話番号で検索"
                                        value="{{ request('search') }}"
                                        class="border rounded px-3 py-2 w-64"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">利用開始日</label>
                                    <input
                                        type="date"
                                        name="start_date"
                                        value="{{ request('start_date') }}"
                                        class="border rounded px-3 py-2"
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">利用終了日</label>
                                    <input
                                        type="date"
                                        name="end_date"
                                        value="{{ request('end_date') }}"
                                        class="border rounded px-3 py-2"
                                    >
                                </div>
                                <div>
                                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                        検索
                                    </button>
                                    @if(request('search') || request('start_date') || request('end_date'))
                                        <a href="{{ route('admin.customers.index') }}" class="ml-2 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                                            クリア
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>

                        {{-- 顧客一覧テーブル --}}
                        <table class="w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-4 py-2 text-left">ID</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">名前</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">メールアドレス</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">電話番号</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">車種</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">利用期間</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">オプション</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                    <tr class="odd:bg-white even:bg-gray-50">
                                        <td class="border border-gray-300 px-4 py-2">{{ $customer->id }}</td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            <a href="{{ route('admin.customers.show', $customer) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                                {{ $customer->name }}
                                            </a>
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            <span class="text-sm text-gray-700">{{ $customer->email }}</span>
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            @if($customer->phone_main)
                                                {{ $customer->phone_main }}
                                            @else
                                                <span class="text-gray-500">未登録</span>
                                            @endif
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            @if($customer->reservations->isEmpty())
                                                <span class="text-gray-500">予約なし</span>
                                            @else
                                                <div class="space-y-1">
                                                    @foreach($customer->reservations->take(3) as $reservation)
                                                        <div class="text-sm font-medium text-gray-700">
                                                            {{ $reservation->car->carModel->name ?? '車両不明' }}
                                                        </div>
                                                    @endforeach
                                                    @if($customer->reservations->count() > 3)
                                                        <div class="text-xs text-gray-500">
                                                            他{{ $customer->reservations->count() - 3 }}件
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            @if($customer->reservations->isEmpty())
                                                <span class="text-gray-500">-</span>
                                            @else
                                                <div class="space-y-1">
                                                    @foreach($customer->reservations->take(3) as $reservation)
                                                        @if($reservation->start_datetime && $reservation->end_datetime)
                                                            <div class="text-sm font-medium text-blue-600">
                                                                {{ $reservation->start_datetime->format('m/d H:i') }} 〜 {{ $reservation->end_datetime->format('m/d H:i') }}
                                                            </div>
                                                        @else
                                                            <div class="text-gray-500 text-xs">日付未設定</div>
                                                        @endif
                                                    @endforeach
                                                    @if($customer->reservations->count() > 3)
                                                        <div class="text-xs text-gray-500">
                                                            他{{ $customer->reservations->count() - 3 }}件
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            @if($customer->latest_options && $customer->latest_options->isNotEmpty())
                                                <div class="space-y-1">
                                                    @foreach($customer->latest_options->take(3) as $option)
                                                        <div class="text-xs">
                                                            <span class="font-medium text-green-600">{{ $option->name }}</span>
                                                            @if($option->price > 0)
                                                                <span class="text-gray-500">¥{{ number_format($option->price) }}</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                    @if($customer->latest_options->count() > 3)
                                                        <div class="text-xs text-gray-500">
                                                            他{{ $customer->latest_options->count() - 3 }}件
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-500 text-xs">なし</span>
                                            @endif
                                        </td>
                                        <td class="border border-gray-300 px-4 py-2">
                                            <a href="{{ route('admin.customers.show', $customer) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                                詳細を見る
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">顧客が見つかりませんでした。</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                    {{-- ページネーション --}}
                    <div class="mt-4">
                        {{ $customers->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
