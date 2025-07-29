<x-user-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('料金表') }}
        </h2>
    </x-slot>
    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">料金プラン一覧</h2>
                @if($currentSeason)
                    <p class="text-gray-600">現在は<span class="font-semibold text-blue-600">{{ $currentSeason->name }}</span>期間です
                        （通常料金{{ $rate < 100 ? 'より'.($rate).'%お得' : 'の'.($rate).'%増' }}）</p>
                @else
                    <p class="text-gray-600">現在は通常期間です。</p>
                @endif
            </div>

            {{-- タブ切り替え --}}
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-200">
                    <nav class="flex" aria-label="Tabs">
                        <button onclick="switchTab('high')"
                            class="tab-button relative min-w-0 flex-1 overflow-hidden py-4 px-4 text-center text-sm font-medium hover:bg-yellow-100 focus:z-10 focus:outline-none"
                            id="tab-high"
                            data-color="yellow">
                            <div class="flex items-center justify-center space-x-2">
                                <svg class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 2zM10 15a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 15zM10 7a3 3 0 100 6 3 3 0 000-6zM15.657 5.404a.75.75 0 10-1.06-1.06l-1.061 1.06a.75.75 0 001.06 1.06l1.06-1.06zM6.464 14.596a.75.75 0 10-1.06-1.06l-1.06 1.06a.75.75 0 001.06 1.06l1.06-1.06zM18 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 0118 10zM5 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 015 10zM14.596 15.657a.75.75 0 001.06-1.06l-1.06-1.061a.75.75 0 10-1.06 1.06l1.06 1.06zM5.404 6.464a.75.75 0 001.06-1.06l-1.06-1.06a.75.75 0 10-1.061 1.06l1.06 1.06z"/>
                                </svg>
                                <div>
                                    <div class="text-base font-semibold">ハイシーズン</div>
                                    <div class="text-xs text-gray-500">通常料金の{{ $seasonTypes['high']?->rate ?? 120 }}%</div>
                                </div>
                            </div>
                        </button>
                        <button onclick="switchTab('normal')"
                            class="tab-button relative min-w-0 flex-1 overflow-hidden py-4 px-4 text-center text-sm font-medium hover:bg-blue-100 focus:z-10 focus:outline-none"
                            id="tab-normal"
                            data-color="blue">
                            <div class="flex items-center justify-center space-x-2">
                                <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-1.5a6.5 6.5 0 100-13 6.5 6.5 0 000 13z" clip-rule="evenodd"/>
                                    <path fill-rule="evenodd" d="M10 5.5a.5.5 0 01.5.5v4.5h4a.5.5 0 010 1h-4.5a.5.5 0 01-.5-.5V6a.5.5 0 01.5-.5z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <div class="text-base font-semibold">通常料金</div>
                                    <div class="text-xs text-gray-500">基本料金</div>
                                </div>
                            </div>
                        </button>
                        <button onclick="switchTab('low')"
                            class="tab-button relative min-w-0 flex-1 overflow-hidden py-4 px-4 text-center text-sm font-medium hover:bg-green-100 focus:z-10 focus:outline-none"
                            id="tab-low"
                            data-color="green">
                            <div class="flex items-center justify-center space-x-2">
                                <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zm0 14.5a6.5 6.5 0 110-13 6.5 6.5 0 010 13z" clip-rule="evenodd"/>
                                    <path d="M5.5 10a.5.5 0 01.5-.5h8a.5.5 0 010 1H6a.5.5 0 01-.5-.5z"/>
                                </svg>
                                <div>
                                    <div class="text-base font-semibold">ローシーズン</div>
                                    <div class="text-xs text-gray-500">通常料金の{{ $seasonTypes['low']?->rate ?? 80 }}%</div>
                                </div>
                            </div>
                        </button>
                    </nav>
                </div>

                {{-- 料金表（各シーズン） --}}
                <div class="p-4">
                    <div id="table-high" class="season-table hidden">
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                            <p class="text-yellow-800">
                                <span class="font-semibold">{{ $seasonTypes['high']?->name ?? 'ハイシーズン' }}</span>
                                期間の料金表です
                            </p>
                            @if($seasonTypes['high'])
                                <p class="text-sm text-gray-600 mt-1">{{ $seasonTypes['high']->description }}</p>
                            @endif
                        </div>
                        <div class="bg-yellow-50/50 rounded-lg p-4 max-h-[600px] overflow-y-auto">
                            @include('user.store.pricing._table', ['rate' => $seasonTypes['high']?->rate ?? 120])
                        </div>
                    </div>

                    <div id="table-normal" class="season-table hidden">
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                            <p class="text-blue-800">
                                <span class="font-semibold">通常</span>
                                期間の料金表です
                            </p>
                            @if($seasonTypes['normal'])
                                <p class="text-sm text-gray-600 mt-1">{{ $seasonTypes['normal']->description }}</p>
                            @endif
                        </div>
                        <div class="bg-blue-50/50 rounded-lg p-4 max-h-[600px] overflow-y-auto">
                            @include('user.store.pricing._table', ['rate' => 100])
                        </div>
                    </div>

                    <div id="table-low" class="season-table hidden">
                        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
                            <p class="text-green-800">
                                <span class="font-semibold">{{ $seasonTypes['low']?->name ?? 'ローシーズン' }}</span>
                                期間の料金表です
                            </p>
                            @if($seasonTypes['low'])
                                <p class="text-sm text-gray-600 mt-1">{{ $seasonTypes['low']->description }}</p>
                            @endif
                        </div>
                        <div class="bg-green-50/50 rounded-lg p-4 max-h-[600px] overflow-y-auto">
                            @include('user.store.pricing._table', ['rate' => $seasonTypes['low']?->rate ?? 80])
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-sm text-gray-500">
                <p>※ 表示価格は税込です。</p>
                <p>※ 延長料金は1時間あたりの料金です。</p>
                <p>※ 48時間以上のご利用をご検討の方は、お電話にてお問い合わせください。</p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // 初期表示（現在のシーズンまたは通常料金を表示）
        document.addEventListener('DOMContentLoaded', function() {
            @if($currentSeason)
                const currentRate = {{ $currentSeason->rate }};
                if (currentRate > 100) {
                    switchTab('high');
                } else if (currentRate < 100) {
                    switchTab('low');
                } else {
                    switchTab('normal');
                }
            @else
                switchTab('normal');
            @endif
        });

        function switchTab(seasonType) {
            // タブのスタイル切り替え
            document.querySelectorAll('.tab-button').forEach(button => {
                const color = button.dataset.color;
                if (button.id === `tab-${seasonType}`) {
                    button.classList.add(`bg-${color}-100`, `border-b-2`, `border-${color}-500`);
                    button.classList.remove('bg-gray-50', 'border-transparent');
                } else {
                    button.classList.remove(`bg-${color}-100`, `border-b-2`, `border-${color}-500`);
                    button.classList.add('bg-gray-50', 'border-transparent');
                }
            });

            // テーブルの表示切り替え
            document.querySelectorAll('.season-table').forEach(table => {
                if (table.id === `table-${seasonType}`) {
                    table.classList.remove('hidden');
                } else {
                    table.classList.add('hidden');
                }
            });
        }
    </script>
    @endpush
</x-user-layout>