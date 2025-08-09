<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('車両タイプ別料金管理') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.car-type-prices.create-yearly') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    年間一括作成
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 全体の月別タブ -->
            <div class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl shadow-lg">
                <div class="px-6 py-6 border-b border-blue-200 bg-white/50 backdrop-blur-sm rounded-t-xl">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="text-xl font-bold text-gray-800">月別表示</h3>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">選択した月の全車両タイプの料金設定を表示</p>
                </div>
                <div class="p-6">
                    <div class="bg-white rounded-lg shadow-sm border border-blue-100">
                        <nav class="flex" aria-label="Tabs">
                            @for($month = 1; $month <= 12; $month++)
                                <button type="button" 
                                    class="global-month-tab flex-1 py-3 px-2 border-b-2 font-medium text-sm transition-all duration-200 {{ $month == date('n') ? 'border-blue-500 text-blue-600 bg-blue-50 shadow-sm' : 'border-transparent text-gray-500 hover:text-blue-600 hover:border-blue-300 hover:bg-blue-25' }}"
                                    data-month="{{ $month }}">
                                    <div class="flex flex-col items-center">
                                        <span class="text-lg font-semibold">{{ $month }}</span>
                                        <span class="text-xs">月</span>
                                    </div>
                                </button>
                            @endfor
                        </nav>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6">
                    <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 車両タイプ別セクション -->
            <div class="space-y-12">
                @forelse ($carTypePrices as $carType => $prices)
                    @php
                        // この車両タイプで料金が設定されている期間があるかチェック
                        $hasValidPrices = false;
                        foreach ($prices as $price) {
                            $seasonPrice = null;
                            if ($price->season_type && isset($seasonPrices[$carType][$price->season_type])) {
                                $seasonPrice = $seasonPrices[$carType][$price->season_type];
                            }
                            if ($seasonPrice && $seasonPrice->price_per_day && $seasonPrice->price_per_day > 0) {
                                $hasValidPrices = true;
                                break;
                            }
                        }
                    @endphp
                    @if($hasValidPrices)
                        <div class="bg-white shadow-sm car-type-section {{ $carType === '軽自動車' ? 'mb-8' : 'mb-8' }}" data-car-type="{{ $carType }}">
                            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $carType }}</h3>
                                    <a href="{{ route('admin.car-type-prices.create-yearly', ['car_type' => $carType, 'year' => date('Y')]) }}" 
                                       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                                        年間一括編集
                                    </a>
                                </div>
                            </div>
                            <div class="p-6">
                                <!-- シーズン料金の概要表示 -->
                                <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">設定済みシーズン料金</h4>
                                    <div class="flex flex-wrap gap-2" id="periods-{{ $carType }}">
                                        @foreach(['high_season' => 'ハイシーズン', 'normal' => '通常', 'low_season' => 'ローシーズン'] as $seasonType => $seasonName)
                                            @php
                                                $seasonPrice = $seasonPrices[$carType][$seasonType] ?? null;
                                                $shouldDisplay = $seasonPrice && $seasonPrice->price_per_day && $seasonPrice->price_per_day > 0;
                                            @endphp
                                            @if($shouldDisplay)
                                                <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-white border border-gray-300 text-gray-700">
                                                    {{ $seasonName }}: ¥{{ number_format($seasonPrice->price_per_day) }}/日
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                
                                <div class="overflow-x-auto mt-6">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">期間</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">適用シーズン</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">料金</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($prices as $price)
                                                @php
                                                    $seasonPrice = null;
                                                    if ($price->season_type && isset($seasonPrices[$carType][$price->season_type])) {
                                                        $seasonPrice = $seasonPrices[$carType][$price->season_type];
                                                    }
                                                    // 料金が設定されている場合のみ表示
                                                    $shouldDisplay = $seasonPrice && $seasonPrice->price_per_day && $seasonPrice->price_per_day > 0;
                                                @endphp
                                                @if($shouldDisplay && $price->start_date && $price->end_date)
                                                    <tr class="price-row" 
                                                        data-start-month="{{ $price->start_date ? $price->start_date->format('n') : '' }}"
                                                        data-end-month="{{ $price->end_date ? $price->end_date->format('n') : '' }}">
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                            <div>{{ $price->getPeriodDisplayName() }}</div>
                                                            @if($price->comment)
                                                                <div class="text-xs text-gray-500">{{ $price->comment }}</div>
                                                            @elseif($price->start_date && $price->end_date)
                                                                <div class="text-xs text-gray-500">
                                                                    {{ $price->start_date->format('Y/m/d') }} - {{ $price->end_date->format('Y/m/d') }}
                                                                </div>
                                                            @endif
                                                            @if($price->notes)
                                                                <div class="text-xs text-gray-400 mt-1">{{ $price->notes }}</div>
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $price->getSeasonDisplayName() }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            ¥{{ number_format($seasonPrice->price_per_day) }}/日
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- 設定されていない場合のメッセージ -->
                                <div class="no-data-message hidden mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-sm text-yellow-800">選択した月の料金設定がありません。</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-8">
                        <div class="text-center">
                            <p class="text-gray-500">車両タイプ別料金が設定されていません。</p>
                            <p class="text-sm text-gray-400 mt-2">年間一括作成で料金設定を追加してください。</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 今日の月を取得
            const currentMonth = new Date().getMonth() + 1; // JavaScriptは0ベースなので+1
            
            // 初期表示で今日の月のデータを表示
            function showCurrentMonthData() {
                const currentMonthTab = document.querySelector(`[data-month="${currentMonth}"]`);
                if (currentMonthTab) {
                    // タブのアクティブ状態を更新
                    document.querySelectorAll('.global-month-tab').forEach(t => {
                        t.classList.remove('border-blue-500', 'text-blue-600', 'bg-blue-50', 'shadow-sm');
                        t.classList.add('border-transparent', 'text-gray-500');
                    });
                    currentMonthTab.classList.remove('border-transparent', 'text-gray-500');
                    currentMonthTab.classList.add('border-blue-500', 'text-blue-600', 'bg-blue-50', 'shadow-sm');
                    
                    // 今日の月のデータを表示
                    document.querySelectorAll('.car-type-section').forEach(section => {
                        const carType = section.dataset.carType;
                        const periodItems = document.querySelectorAll(`#periods-${carType} .period-item`);
                        
                        // 期間の表示をフィルタリング
                        periodItems.forEach(item => {
                            const startMonth = item.dataset.startMonth;
                            const endMonth = item.dataset.endMonth;
                            
                            if (startMonth && endMonth) {
                                // 期間が設定されている場合、選択された月が期間内かチェック
                                const selectedMonth = parseInt(currentMonth);
                                const start = parseInt(startMonth);
                                const end = parseInt(endMonth);
                                
                                if ((start <= selectedMonth && selectedMonth <= end) || 
                                    (start > end && (selectedMonth >= start || selectedMonth <= end))) {
                                    item.style.display = 'inline-flex';
                                } else {
                                    item.style.display = 'none';
                                }
                            } else {
                                // シーズン料金の場合は常に表示
                                item.style.display = 'inline-flex';
                            }
                        });
                        
                        // テーブル行の表示をフィルタリング
                        const priceRows = section.querySelectorAll('.price-row');
                        priceRows.forEach(row => {
                            const startMonth = row.dataset.startMonth;
                            const endMonth = row.dataset.endMonth;
                            
                            if (startMonth && endMonth) {
                                // 期間が設定されている場合、選択された月が期間内かチェック
                                const selectedMonth = parseInt(currentMonth);
                                const start = parseInt(startMonth);
                                const end = parseInt(endMonth);
                                
                                if ((start <= selectedMonth && selectedMonth <= end) || 
                                    (start > end && (selectedMonth >= start || selectedMonth <= end))) {
                                    row.style.display = 'table-row';
                                } else {
                                    row.style.display = 'none';
                                }
                            } else {
                                // シーズン料金の場合は常に表示
                                row.style.display = 'table-row';
                            }
                        });
                        
                        // 車両タイプセクション全体の表示/非表示を制御
                        const hasVisibleItems = Array.from(periodItems).some(item => item.style.display !== 'none');
                        const hasVisibleRows = Array.from(priceRows).some(row => row.style.display !== 'none');
                        
                        if (hasVisibleItems || hasVisibleRows) {
                            section.style.display = 'block';
                            // メッセージを非表示
                            const noDataMessage = section.querySelector('.no-data-message');
                            if (noDataMessage) {
                                noDataMessage.classList.add('hidden');
                            }
                        } else {
                            section.style.display = 'block';
                            // メッセージを表示
                            const noDataMessage = section.querySelector('.no-data-message');
                            if (noDataMessage) {
                                noDataMessage.classList.remove('hidden');
                            }
                        }
                    });
                }
            }
            
            // 初期表示を実行
            showCurrentMonthData();
            
            // 全体の月別タブのイベントリスナー
            document.querySelectorAll('.global-month-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    const month = this.dataset.month;
                    
                    // 全体のタブのアクティブ状態を更新
                    document.querySelectorAll('.global-month-tab').forEach(t => {
                        t.classList.remove('border-blue-500', 'text-blue-600', 'bg-blue-50', 'shadow-sm');
                        t.classList.add('border-transparent', 'text-gray-500');
                    });
                    this.classList.remove('border-transparent', 'text-gray-500');
                    this.classList.add('border-blue-500', 'text-blue-600', 'bg-blue-50', 'shadow-sm');
                    
                    // 全車両タイプセクションの表示を切り替え
                    document.querySelectorAll('.car-type-section').forEach(section => {
                        const carType = section.dataset.carType;
                        const periodItems = document.querySelectorAll(`#periods-${carType} .period-item`);
                        
                        // 期間の表示をフィルタリング
                        periodItems.forEach(item => {
                            const startMonth = item.dataset.startMonth;
                            const endMonth = item.dataset.endMonth;
                            
                            if (startMonth && endMonth) {
                                // 期間が設定されている場合、選択された月が期間内かチェック
                                const selectedMonth = parseInt(month);
                                const start = parseInt(startMonth);
                                const end = parseInt(endMonth);
                                
                                if ((start <= selectedMonth && selectedMonth <= end) || 
                                    (start > end && (selectedMonth >= start || selectedMonth <= end))) {
                                    item.style.display = 'inline-flex';
                                } else {
                                    item.style.display = 'none';
                                }
                            } else {
                                // シーズン料金の場合は常に表示
                                item.style.display = 'inline-flex';
                            }
                        });
                        
                        // テーブル行の表示をフィルタリング
                        const priceRows = section.querySelectorAll('.price-row');
                        priceRows.forEach(row => {
                            const startMonth = row.dataset.startMonth;
                            const endMonth = row.dataset.endMonth;
                            
                            if (startMonth && endMonth) {
                                // 期間が設定されている場合、選択された月が期間内かチェック
                                const selectedMonth = parseInt(month);
                                const start = parseInt(startMonth);
                                const end = parseInt(endMonth);
                                
                                if ((start <= selectedMonth && selectedMonth <= end) || 
                                    (start > end && (selectedMonth >= start || selectedMonth <= end))) {
                                    row.style.display = 'table-row';
                                } else {
                                    row.style.display = 'none';
                                }
                            } else {
                                // シーズン料金の場合は常に表示
                                row.style.display = 'table-row';
                            }
                        });
                        
                        // 車両タイプセクション全体の表示/非表示を制御
                        const hasVisibleItems = Array.from(periodItems).some(item => item.style.display !== 'none');
                        const hasVisibleRows = Array.from(priceRows).some(row => row.style.display !== 'none');
                        
                        if (hasVisibleItems || hasVisibleRows) {
                            section.style.display = 'block';
                            // メッセージを非表示
                            const noDataMessage = section.querySelector('.no-data-message');
                            if (noDataMessage) {
                                noDataMessage.classList.add('hidden');
                            }
                        } else {
                            section.style.display = 'block';
                            // メッセージを表示
                            const noDataMessage = section.querySelector('.no-data-message');
                            if (noDataMessage) {
                                noDataMessage.classList.remove('hidden');
                            }
                        }
                    });
                });
            });
        });
    </script>
</x-admin-layout> 