<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                @if($selectedCarType && $selectedYear)
                    {{ $selectedCarType }} - {{ $selectedYear }}年 年間一括編集
                @else
                    {{ __('車両タイプ別料金設定') }}
                @endif
            </h2>
            <a href="{{ route('admin.car-type-prices.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
                一覧に戻る
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if ($errors->any())
                        <div class="mb-4">
                            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.car-type-prices.store-yearly') }}" id="yearlyForm">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="year" class="block text-sm font-medium text-gray-700">対象年 <span class="text-red-500">*</span></label>
                                <select id="year" name="year" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-200 focus:border-indigo-300">
                                    <option value="">選択してください</option>
                                    @for($y = 2024; $y <= 2030; $y++)
                                        <option value="{{ $y }}" {{ old('year', $selectedYear ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}年</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <!-- 車両タイプタブ -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold mb-4 text-gray-800">車両タイプ <span class="text-red-500">*</span></h3>
                            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                                <div class="border-b border-gray-200">
                                    <nav class="flex" aria-label="Tabs">
                                        @foreach($carTypes as $key => $value)
                                                                                        <button type="button" 
                                                class="car-type-tab flex-1 min-w-0 py-2 px-3 text-center font-medium text-xs transition-all duration-300 ease-in-out {{ $key === ($selectedCarType ?? array_key_first($carTypes)) ? 'bg-blue-50 text-blue-700 border-b-2 border-blue-500 shadow-sm' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-25' }}"
                                                data-car-type="{{ $key }}"
                                                data-car-type-name="{{ $value }}">
                                                <span>{{ $value }}</span>
                                            </button>
                                        @endforeach
                                    </nav>
                                </div>
                            </div>
                            <input type="hidden" id="car_type" name="car_type" value="{{ old('car_type', $selectedCarType ?? array_key_first($carTypes)) }}" required />
                        </div>



                        <!-- シーズン料金設定セクション -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold mb-4 text-gray-800">シーズン料金設定 <span class="text-red-500">*</span></h3>
                            <p class="text-sm text-gray-600 mb-4">各シーズンの1日あたりの料金を設定してください。シーズン料金を設定した車両タイプは、今年中の料金設定が必須です。</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- ハイシーズン料金 -->
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <div class="flex items-center mb-3">
                                        <svg class="h-5 w-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 2a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 2zM10 15a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 15zM10 7a3 3 0 100 6 3 3 0 000-6zM15.657 5.404a.75.75 0 10-1.06-1.06l-1.061 1.06a.75.75 0 001.06 1.06l1.06-1.06zM6.464 14.596a.75.75 0 10-1.06-1.06l-1.06 1.06a.75.75 0 001.06 1.06l1.06-1.06zM18 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 0118 10zM5 10a.75.75 0 01-.75.75h-1.5a.75.75 0 010-1.5h1.5A.75.75 0 015 10zM14.596 15.657a.75.75 0 001.06-1.06l-1.06-1.061a.75.75 0 10-1.06 1.06l1.06 1.06zM5.404 6.464a.75.75 0 001.06-1.06l-1.06-1.06a.75.75 0 10-1.061 1.06l1.06 1.06z"/>
                                        </svg>
                                        <h4 class="text-lg font-semibold text-yellow-800">ハイシーズン料金</h4>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">1日あたりの料金 <span class="text-red-500">*</span></label>
                                        <input type="number" name="season_prices[high_season][price_per_day]" required 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-yellow-200 focus:border-yellow-300" 
                                            placeholder="8000" min="1" 
                                            value="{{ old('season_prices.high_season.price_per_day', $previousInput['season_prices']['high_season']['price_per_day'] ?? ($existingSeasonPrices['high_season']->price_per_day ?? '')) }}" />
                                        <p class="mt-1 text-xs text-gray-500">必ず入力してください</p>
                                    </div>
                                </div>

                                <!-- 通常料金 -->
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex items-center mb-3">
                                        <svg class="h-5 w-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-1.5a6.5 6.5 0 100-13 6.5 6.5 0 000 13z" clip-rule="evenodd"/>
                                            <path fill-rule="evenodd" d="M10 5.5a.5.5 0 01.5.5v4.5h4a.5.5 0 010 1h-4.5a.5.5 0 01-.5-.5V6a.5.5 0 01.5-.5z" clip-rule="evenodd"/>
                                        </svg>
                                        <h4 class="text-lg font-semibold text-blue-800">通常料金</h4>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">1日あたりの料金 <span class="text-red-500">*</span></label>
                                        <input type="number" name="season_prices[normal][price_per_day]" required 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-200 focus:border-blue-300" 
                                            placeholder="6000" min="1" 
                                            value="{{ old('season_prices.normal.price_per_day', $previousInput['season_prices']['normal']['price_per_day'] ?? ($existingSeasonPrices['normal']->price_per_day ?? '')) }}" />
                                        <p class="mt-1 text-xs text-gray-500">必ず入力してください</p>
                                    </div>
                                </div>

                                <!-- ローシーズン料金 -->
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <div class="flex items-center mb-3">
                                        <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/>
                                        </svg>
                                        <h4 class="text-lg font-semibold text-green-800">ローシーズン料金</h4>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">1日あたりの料金 <span class="text-red-500">*</span></label>
                                        <input type="number" name="season_prices[low_season][price_per_day]" required 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-green-200 focus:border-blue-300" 
                                            placeholder="4000" min="1" 
                                            value="{{ old('season_prices.low_season.price_per_day', $previousInput['season_prices']['low_season']['price_per_day'] ?? ($existingSeasonPrices['low_season']->price_per_day ?? '')) }}" />
                                        <p class="mt-1 text-xs text-gray-500">必ず入力してください</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 期間設定セクション -->
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-800">期間設定 <span class="text-red-500">*</span></h3>
                            </div>
                            <p class="text-sm text-gray-600 mb-4">シーズン料金を設定した場合は、今年中の期間設定が必須です。少なくとも1つの期間を設定してください。</p>
                            
                            <!-- 月別タブ -->
                            <div class="mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl shadow-lg">
                                <div class="px-6 py-4 border-b border-blue-200 bg-white/50 backdrop-blur-sm rounded-t-xl">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <h4 class="text-lg font-semibold text-gray-800">月別期間設定</h4>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">各月の期間設定を確認・管理できます</p>
                                </div>
                                <div class="p-6">
                                    <div class="bg-white rounded-lg shadow-sm border border-blue-100">
                                        <nav class="flex flex-wrap" aria-label="Month Tabs">
                                            @php
                                                $currentMonth = date('n');
                                                $currentYear = date('Y');
                                                $selectedYear = $selectedYear ?? $currentYear;
                                            @endphp
                                            @for($month = 1; $month <= 12; $month++)
                                                @php
                                                    $isCurrentYear = $selectedYear == $currentYear;
                                                    $isPastMonth = $isCurrentYear && $month < $currentMonth;
                                                    $isActiveMonth = $month == $currentMonth;
                                                    $isDisabled = $isPastMonth;
                                                @endphp
                                                <button type="button" 
                                                    class="month-tab flex-1 min-w-0 py-3 px-2 border-b-2 font-medium text-sm transition-all duration-200 {{ $isActiveMonth ? 'border-blue-500 text-blue-600 bg-blue-50 shadow-sm' : ($isDisabled ? 'border-transparent text-gray-300 cursor-not-allowed' : 'border-transparent text-gray-500 hover:text-blue-600 hover:border-blue-300 hover:bg-blue-25') }}"
                                                    data-month="{{ $month }}"
                                                    {{ $isDisabled ? 'disabled' : '' }}>
                                                    <div class="flex flex-col items-center">
                                                        <span class="text-lg font-semibold">{{ $month }}</span>
                                                        <span class="text-xs">月</span>
                                                        @if($isDisabled)
                                                            <span class="text-xs text-gray-400">過去</span>
                                                        @endif
                                                    </div>
                                                </button>
                                            @endfor
                                        </nav>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="periods-container">
                                <!-- 期間はJavaScriptで動的に追加 -->
                            </div>
                            
                            <!-- 期間を追加ボタン -->
                            <div class="mt-4 text-center">
                                <button type="button" id="add-period" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    期間を追加
                                </button>
                            </div>
                        </div>



                        <!-- 注意書き -->
                        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <h4 class="text-sm font-medium text-yellow-800">重要</h4>
                                    <p class="text-sm text-yellow-700 mt-1">
                                        シーズン料金を設定した場合は、今年中の期間設定が必須です。少なくとも1つの期間を設定してください。
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('admin.car-type-prices.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                キャンセル
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                保存
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let periodCounter = 0;

        // 期間行を作成する関数
        function createPeriodRow(suggestedStartDate = null) {
            periodCounter++;
            const row = document.createElement('div');
            row.className = 'period-row border border-gray-200 rounded-lg p-4 mb-4';
            
            // 現在選択されている月を取得
            const activeMonthTab = document.querySelector('.month-tab.border-blue-500');
            const selectedMonth = activeMonthTab ? parseInt(activeMonthTab.dataset.month) : new Date().getMonth() + 1;
            const year = document.getElementById('year').value || new Date().getFullYear();
            const daysInMonth = new Date(year, selectedMonth, 0).getDate();
            const monthStr = selectedMonth.toString().padStart(2, '0');
            
            // 現在の日付を取得（過去の日付を選択できないようにするため）
            const today = new Date();
            const todayStr = today.toISOString().split('T')[0];
            
            // 開始日を設定（期間1の場合は1日、期間2以降は前の期間の終了日の次の日）
            let startDate;
            if (periodCounter === 1) {
                // 期間1の場合は1日から開始（ただし今日以降）
                const firstDayOfMonth = `${year}-${monthStr}-01`;
                startDate = firstDayOfMonth >= todayStr ? firstDayOfMonth : todayStr;
            } else {
                // 期間2以降は前の期間の終了日の次の日
                if (suggestedStartDate) {
                    startDate = suggestedStartDate >= todayStr ? suggestedStartDate : todayStr;
                } else {
                    // 前の期間の終了日を取得
                    const previousPeriod = document.querySelector(`.period-row:nth-child(${periodCounter - 1})`);
                    if (previousPeriod) {
                        const previousEndDate = previousPeriod.querySelector('input[name*="[end_date]"]').value;
                        if (previousEndDate) {
                            const endDate = new Date(previousEndDate);
                            endDate.setDate(endDate.getDate() + 1);
                            const nextDay = endDate.toISOString().split('T')[0];
                            startDate = nextDay >= todayStr ? nextDay : todayStr;
                        } else {
                            startDate = todayStr;
                        }
                    } else {
                        startDate = todayStr;
                    }
                }
            }
            
            row.innerHTML = `
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-md font-semibold text-gray-800">期間${periodCounter}</h4>
                    <button type="button" class="remove-period text-red-600 hover:text-red-800 text-sm">削除</button>
                </div>
                <div class="flex flex-col md:flex-row gap-3">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-700">開始日 <span class="text-red-500">*</span></label>
                        <input type="date" name="periods[${periodCounter-1}][start_date]" required 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-200 focus:border-indigo-300 period-start-date text-sm"
                            value="${startDate}" 
                            min="${todayStr}"
                            max="${year}-${monthStr}-${daysInMonth}" />
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-700">終了日 <span class="text-red-500">*</span></label>
                        <input type="date" name="periods[${periodCounter-1}][end_date]" required 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-200 focus:border-indigo-300 period-end-date text-sm"
                            min="${todayStr}"
                            max="${year}-${monthStr}-${daysInMonth}" />
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-700">適用するシーズン料金 <span class="text-red-500">*</span></label>
                        <select name="periods[${periodCounter-1}][season_type]" required 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-200 focus:border-indigo-300 text-sm">
                            <option value="">選択してください</option>
                            <option value="high_season">ハイシーズン</option>
                            <option value="normal">通常</option>
                            <option value="low_season">ローシーズン</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-700">コメント</label>
                        <input type="text" name="periods[${periodCounter-1}][comment]" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-200 focus:border-indigo-300 text-sm"
                            placeholder="例：ゴールデンウィーク、春休み" />
                    </div>
                </div>
            `;
            return row;
        }

        // 期間の終了日変更時に次の期間の開始日を自動更新する関数
        function updateNextPeriodStartDate(changedPeriodRow) {
            const allPeriodRows = document.querySelectorAll('.period-row');
            const changedIndex = Array.from(allPeriodRows).indexOf(changedPeriodRow);
            
            // 変更された期間の次の期間から順番に開始日を更新
            for (let i = changedIndex + 1; i < allPeriodRows.length; i++) {
                const currentRow = allPeriodRows[i];
                const previousRow = allPeriodRows[i - 1];
                
                const previousEndDate = previousRow.querySelector('.period-end-date').value;
                const currentStartDate = currentRow.querySelector('.period-start-date');
                
                if (previousEndDate && currentStartDate) {
                    // 前の期間の終了日の翌日を計算
                    const endDate = new Date(previousEndDate);
                    endDate.setDate(endDate.getDate() + 1);
                    const nextDay = endDate.toISOString().split('T')[0];
                    
                    // 現在の期間の開始日を更新
                    currentStartDate.value = nextDay;
                    
                    // 開始日の最小値も更新
                    const year = document.getElementById('year').value || new Date().getFullYear();
                    const activeMonthTab = document.querySelector('.month-tab.border-blue-500');
                    const selectedMonth = activeMonthTab ? parseInt(activeMonthTab.dataset.month) : new Date().getMonth() + 1;
                    const monthStr = selectedMonth.toString().padStart(2, '0');
                    currentStartDate.min = `${year}-${monthStr}-01`;
                }
            }
        }

        // 期間の開始日変更時に前の期間の終了日を自動調整する関数
        function updatePreviousPeriodEndDate(changedPeriodRow) {
            const allPeriodRows = document.querySelectorAll('.period-row');
            const changedIndex = Array.from(allPeriodRows).indexOf(changedPeriodRow);
            
            if (changedIndex > 0) {
                const currentRow = changedPeriodRow;
                const previousRow = allPeriodRows[changedIndex - 1];
                
                const currentStartDate = currentRow.querySelector('.period-start-date').value;
                const previousEndDate = previousRow.querySelector('.period-end-date');
                
                if (currentStartDate && previousEndDate) {
                    // 現在の期間の開始日の前日を計算
                    const startDate = new Date(currentStartDate);
                    startDate.setDate(startDate.getDate() - 1);
                    const previousDay = startDate.toISOString().split('T')[0];
                    
                    // 前の期間の終了日を更新
                    previousEndDate.value = previousDay;
                }
            }
        }

                    // 月別タブの機能
            function initializeMonthTabs() {
                const monthTabs = document.querySelectorAll('.month-tab');
                const periodRows = document.querySelectorAll('.period-row');
                
                monthTabs.forEach(tab => {
                    tab.addEventListener('click', function() {
                        // 無効化されたタブはクリックできないようにする
                        if (this.disabled) {
                            return;
                        }
                        
                        const selectedMonth = parseInt(this.dataset.month);
                        const year = document.getElementById('year').value || new Date().getFullYear();
                    
                    // タブのアクティブ状態を更新
                    monthTabs.forEach(t => {
                        t.classList.remove('border-blue-500', 'text-blue-600', 'bg-blue-50', 'shadow-sm');
                        t.classList.add('border-transparent', 'text-gray-500');
                    });
                    this.classList.remove('border-transparent', 'text-gray-500');
                    this.classList.add('border-blue-500', 'text-blue-600', 'bg-blue-50', 'shadow-sm');
                    
                    // 選択された月の日数を取得
                    const daysInMonth = new Date(year, selectedMonth, 0).getDate();
                    const monthStr = selectedMonth.toString().padStart(2, '0');
                    
                    // 現在の日付を取得
                    const today = new Date();
                    const todayStr = today.toISOString().split('T')[0];
                    
                    // 期間設定の日付入力フィールドを制限
                    periodRows.forEach((row, index) => {
                        const startDateInput = row.querySelector('input[name*="[start_date]"]');
                        const endDateInput = row.querySelector('input[name*="[end_date]"]');
                        
                        if (startDateInput) {
                            if (index === 0) {
                                // 期間1の場合は1日に固定（ただし今日以降）
                                const firstDayOfMonth = `${year}-${monthStr}-01`;
                                const startDate = firstDayOfMonth >= todayStr ? firstDayOfMonth : todayStr;
                                startDateInput.min = todayStr;
                                startDateInput.max = `${year}-${monthStr}-${daysInMonth}`;
                                startDateInput.value = startDate;
                            } else {
                                // 期間2以降は前の期間の終了日の次の日を設定
                                const previousRow = periodRows[index - 1];
                                const previousEndDate = previousRow.querySelector('input[name*="[end_date]"]').value;
                                
                                if (previousEndDate) {
                                    const endDate = new Date(previousEndDate);
                                    endDate.setDate(endDate.getDate() + 1);
                                    const nextDay = endDate.toISOString().split('T')[0];
                                    const startDate = nextDay >= todayStr ? nextDay : todayStr;
                                    
                                    startDateInput.min = todayStr;
                                    startDateInput.max = `${year}-${monthStr}-${daysInMonth}`;
                                    startDateInput.value = startDate;
                                } else {
                                    startDateInput.min = todayStr;
                                    startDateInput.max = `${year}-${monthStr}-${daysInMonth}`;
                                }
                            }
                        }
                        
                        if (endDateInput) {
                            endDateInput.min = todayStr;
                            endDateInput.max = `${year}-${monthStr}-${daysInMonth}`;
                        }
                    });
                    
                    // 月タブクリック時に該当する期間を表示・作成
                    let monthPeriodFound = false;
                    
                    // 既存の期間行で該当月の期間があるかチェックし、該当しない期間は非表示にする
                    periodRows.forEach(row => {
                        const startDateInput = row.querySelector('input[name*="[start_date]"]');
                        const endDateInput = row.querySelector('input[name*="[end_date]"]');
                        
                        if (startDateInput.value && endDateInput.value) {
                            const startDate = new Date(startDateInput.value);
                            const endDate = new Date(endDateInput.value);
                            const startMonth = startDate.getMonth() + 1;
                            const endMonth = endDate.getMonth() + 1;
                            
                            // 選択された月が期間内かチェック
                            let isInPeriod = false;
                            if (startMonth <= endMonth) {
                                isInPeriod = selectedMonth >= startMonth && selectedMonth <= endMonth;
                            } else {
                                isInPeriod = selectedMonth >= startMonth || selectedMonth <= endMonth;
                            }
                            
                            if (isInPeriod) {
                                row.style.display = 'block';
                                row.classList.add('bg-blue-50', 'border-blue-300');
                                row.classList.remove('border-gray-200');
                                monthPeriodFound = true;
                            } else {
                                row.style.display = 'none'; // 該当しない期間は非表示
                            }
                        } else {
                            // 日付が未設定の場合は非表示
                            row.style.display = 'none';
                        }
                    });
                    
                    // 該当月の期間が見つからない場合、新しい期間を作成
                    if (!monthPeriodFound) {
                        const container = document.getElementById('periods-container');
                        
                        // 既存の期間を全て非表示にする
                        const allRows = container.querySelectorAll('.period-row');
                        allRows.forEach(row => {
                            row.style.display = 'none';
                        });
                        
                        const newRow = createPeriodRow();
                        container.appendChild(newRow);
                        
                        // 月の最初と最後の日を設定
                        const startDateInput = newRow.querySelector('input[name*="[start_date]"]');
                        const endDateInput = newRow.querySelector('input[name*="[end_date]"]');
                        
                        if (startDateInput && endDateInput) {
                            const firstDay = `${year}-${monthStr}-01`;
                            const lastDay = `${year}-${monthStr}-${daysInMonth}`;
                            
                            // 今日以降の日付に調整
                            const today = new Date();
                            const todayStr = today.toISOString().split('T')[0];
                            
                            startDateInput.value = firstDay >= todayStr ? firstDay : todayStr;
                            endDateInput.value = lastDay >= todayStr ? lastDay : todayStr;
                            startDateInput.min = todayStr;
                            startDateInput.max = lastDay;
                            endDateInput.min = todayStr;
                            endDateInput.max = lastDay;
                            
                            // ハイライトを適用
                            newRow.classList.add('bg-blue-50', 'border-blue-300');
                            newRow.classList.remove('border-gray-200');
                            newRow.style.display = 'block';
                        }
                        
                        // イベントリスナーを再初期化
                        initializePeriodEventListeners();
                        
                        // 期間番号を更新
                        updatePeriodNumbers();
                    }
                });
            });
        }

        // 期間のイベントリスナーを初期化する関数
        function initializePeriodEventListeners() {
            const periodRows = document.querySelectorAll('.period-row');
            
            periodRows.forEach(row => {
                const startDateInput = row.querySelector('.period-start-date');
                const endDateInput = row.querySelector('.period-end-date');
                const deleteButton = row.querySelector('.delete-period');
                
                // 既にイベントリスナーが設定されている場合は重複を避ける
                if (startDateInput && !startDateInput.hasAttribute('data-listener-added')) {
                    startDateInput.addEventListener('change', function() {
                        updatePreviousPeriodEndDate(row);
                        updateNextPeriodStartDate(row);
                    });
                    startDateInput.setAttribute('data-listener-added', 'true');
                }
                
                if (endDateInput && !endDateInput.hasAttribute('data-listener-added')) {
                    endDateInput.addEventListener('change', function() {
                        updateNextPeriodStartDate(row);
                    });
                    endDateInput.setAttribute('data-listener-added', 'true');
                }
                
                if (deleteButton && !deleteButton.hasAttribute('data-listener-added')) {
                    deleteButton.addEventListener('click', function() {
                        row.remove();
                        updatePeriodNumbers();
                    });
                    deleteButton.setAttribute('data-listener-added', 'true');
                }
            });
        }

        // 期間番号を更新する関数
        function updatePeriodNumbers() {
            const periodRows = document.querySelectorAll('.period-row');
            periodRows.forEach((row, index) => {
                const titleElement = row.querySelector('h4');
                if (titleElement) {
                    titleElement.textContent = `期間${index + 1}`;
                }
                
                // name属性も更新
                const inputs = row.querySelectorAll('input, select');
                inputs.forEach(input => {
                    if (input.name) {
                        input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
                    }
                });
            });
        }

        // 初期期間を追加
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('periods-container');
            
            // 既存データから期間を復元
            @if(isset($existingData) && $existingData->isNotEmpty())
                @foreach($existingData as $index => $data)
                    const row{{ $index }} = createPeriodRow();
                    container.appendChild(row{{ $index }});
                    
                    // 既存データを設定
                    const startDateInput{{ $index }} = row{{ $index }}.querySelector('input[name*="[start_date]"]');
                    const endDateInput{{ $index }} = row{{ $index }}.querySelector('input[name*="[end_date]"]');
                    const seasonTypeSelect{{ $index }} = row{{ $index }}.querySelector('select[name*="[season_type]"]');
                    const commentInput{{ $index }} = row{{ $index }}.querySelector('input[name*="[comment]"]');
                    
                    if (startDateInput{{ $index }}) startDateInput{{ $index }}.value = '{{ $data->start_date->format('Y-m-d') }}';
                    if (endDateInput{{ $index }}) endDateInput{{ $index }}.value = '{{ $data->end_date->format('Y-m-d') }}';
                    if (seasonTypeSelect{{ $index }}) seasonTypeSelect{{ $index }}.value = '{{ $data->season_type }}';
                    if (commentInput{{ $index }}) commentInput{{ $index }}.value = '{{ $data->comment ?? '' }}';
                @endforeach
            @else
                // 既存データがない場合は初期期間を追加
                container.appendChild(createPeriodRow());
            @endif

            // 月別タブを初期化
            initializeMonthTabs();
            
            // 期間のイベントリスナーを初期化
            initializePeriodEventListeners();
            
            // 初期表示時に過去月の制限を適用
            updateMonthTabRestrictions();
            
            // 年選択時の過去月制限を更新する関数
            function updateMonthTabRestrictions() {
                const year = document.getElementById('year').value || new Date().getFullYear();
                const currentYear = new Date().getFullYear();
                const currentMonth = new Date().getMonth() + 1;
                
                const monthTabs = document.querySelectorAll('.month-tab');
                monthTabs.forEach(tab => {
                    const month = parseInt(tab.dataset.month);
                    const isCurrentYear = year == currentYear;
                    const isPastMonth = isCurrentYear && month < currentMonth;
                    
                    if (isPastMonth) {
                        tab.disabled = true;
                        tab.classList.add('border-transparent', 'text-gray-300', 'cursor-not-allowed');
                        tab.classList.remove('border-blue-500', 'text-blue-600', 'bg-blue-50', 'shadow-sm', 'text-gray-500', 'hover:text-blue-600', 'hover:border-blue-300', 'hover:bg-blue-25');
                        
                        // 「過去」ラベルを追加
                        let pastLabel = tab.querySelector('.text-xs.text-gray-400');
                        if (!pastLabel) {
                            pastLabel = document.createElement('span');
                            pastLabel.className = 'text-xs text-gray-400';
                            pastLabel.textContent = '過去';
                            tab.querySelector('.flex.flex-col.items-center').appendChild(pastLabel);
                        }
                    } else {
                        tab.disabled = false;
                        tab.classList.remove('border-transparent', 'text-gray-300', 'cursor-not-allowed');
                        tab.classList.add('border-transparent', 'text-gray-500');
                        
                        // 「過去」ラベルを削除
                        const pastLabel = tab.querySelector('.text-xs.text-gray-400');
                        if (pastLabel) {
                            pastLabel.remove();
                        }
                    }
                });
            }

            // 初期表示時に現在の月の制限を適用
            const currentMonth = new Date().getMonth() + 1;
            const year = document.getElementById('year').value || new Date().getFullYear();
            const daysInMonth = new Date(year, currentMonth, 0).getDate();
            const monthStr = currentMonth.toString().padStart(2, '0');
            
            // 現在の日付を取得
            const today = new Date();
            const todayStr = today.toISOString().split('T')[0];
            
            const initialPeriodRow = container.querySelector('.period-row');
            if (initialPeriodRow) {
                const startDateInput = initialPeriodRow.querySelector('input[name*="[start_date]"]');
                const endDateInput = initialPeriodRow.querySelector('input[name*="[end_date]"]');
                
                if (startDateInput) {
                    // 期間1の場合は1日に固定（ただし今日以降）
                    const firstDayOfMonth = `${year}-${monthStr}-01`;
                    const startDate = firstDayOfMonth >= todayStr ? firstDayOfMonth : todayStr;
                    startDateInput.min = todayStr;
                    startDateInput.max = `${year}-${monthStr}-${daysInMonth}`;
                    startDateInput.value = startDate;
                }
                
                if (endDateInput) {
                    endDateInput.min = todayStr;
                    endDateInput.max = `${year}-${monthStr}-${daysInMonth}`;
                }
            }

            // 期間追加ボタンのイベント
            document.getElementById('add-period').addEventListener('click', function() {
                const container = document.getElementById('periods-container');
                const lastPeriod = container.lastElementChild;
                const lastEndDate = lastPeriod.querySelector('input[name*="[end_date]"]').value;
                
                // 前の期間の終了日の次の日を計算
                let suggestedStartDate = null;
                if (lastEndDate) {
                    const endDate = new Date(lastEndDate);
                    endDate.setDate(endDate.getDate() + 1);
                    suggestedStartDate = endDate.toISOString().split('T')[0];
                }
                
                container.appendChild(createPeriodRow(suggestedStartDate));
                
                // 新しい期間行が追加された後、月別タブの機能を再初期化
                setTimeout(() => {
                    initializeMonthTabs();
                    initializePeriodEventListeners();
                    
                    // 新しい期間の開始日を前の期間の終了日の翌日に設定
                    const newPeriod = container.lastElementChild;
                    const previousPeriod = container.children[container.children.length - 2];
                    
                    if (previousPeriod && newPeriod) {
                        const previousEndDate = previousPeriod.querySelector('.period-end-date').value;
                        const newStartDate = newPeriod.querySelector('.period-start-date');
                        
                        if (previousEndDate && newStartDate) {
                            const endDate = new Date(previousEndDate);
                            endDate.setDate(endDate.getDate() + 1);
                            const nextDay = endDate.toISOString().split('T')[0];
                            newStartDate.value = nextDay;
                        }
                    }
                }, 100);
            });

            // 期間削除ボタンのイベント（委譲）
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-period')) {
                    const periodRow = e.target.closest('.period-row');
                    const container = document.getElementById('periods-container');
                    const allPeriodRows = container.querySelectorAll('.period-row');
                    const deletedIndex = Array.from(allPeriodRows).indexOf(periodRow);
                    
                    periodRow.remove();
                    
                    // 期間が削除された後、残りの期間の開始日を自動調整
                    setTimeout(() => {
                        initializeMonthTabs();
                        
                        // 削除された期間の次の期間から順番に開始日を更新
                        const remainingPeriodRows = container.querySelectorAll('.period-row');
                        for (let i = deletedIndex; i < remainingPeriodRows.length; i++) {
                            const currentRow = remainingPeriodRows[i];
                            const previousRow = i > 0 ? remainingPeriodRows[i - 1] : null;
                            
                            if (previousRow) {
                                const previousEndDate = previousRow.querySelector('.period-end-date').value;
                                const currentStartDate = currentRow.querySelector('.period-start-date');
                                
                                if (previousEndDate && currentStartDate) {
                                    const endDate = new Date(previousEndDate);
                                    endDate.setDate(endDate.getDate() + 1);
                                    const nextDay = endDate.toISOString().split('T')[0];
                                    currentStartDate.value = nextDay;
                                }
                            }
                        }
                    }, 100);
                }
            });

            // 期間の日付変更時の処理
            document.addEventListener('change', function(e) {
                if (e.target.name && (e.target.name.includes('[start_date]') || e.target.name.includes('[end_date]'))) {
                    const periodRow = e.target.closest('.period-row');
                    
                    // 終了日が変更された場合、次の期間の開始日を自動更新
                    if (e.target.name.includes('[end_date]')) {
                        updateNextPeriodStartDate(periodRow);
                    }
                    
                    // 開始日が変更された場合、前の期間の終了日を自動調整
                    if (e.target.name.includes('[start_date]')) {
                        updatePreviousPeriodEndDate(periodRow);
                    }
                    
                    // 重複チェック
                    const overlaps = checkPeriodOverlaps();
                    
                    if (overlaps.length > 0) {
                        // 重複がある場合、該当する期間行をハイライト
                        periodRow.style.borderColor = '#ef4444';
                        periodRow.style.backgroundColor = '#fef2f2';
                        
                        // エラーメッセージを表示
                        let errorDiv = periodRow.querySelector('.overlap-error');
                        if (!errorDiv) {
                            errorDiv = document.createElement('div');
                            errorDiv.className = 'overlap-error text-red-600 text-xs mt-2';
                            periodRow.appendChild(errorDiv);
                        }
                        errorDiv.textContent = '期間が重複しています。';
                    } else {
                        // 重複がない場合、ハイライトを解除
                        periodRow.style.borderColor = '';
                        periodRow.style.backgroundColor = '';
                        
                        const errorDiv = periodRow.querySelector('.overlap-error');
                        if (errorDiv) {
                            errorDiv.remove();
                        }
                    }
                }
            });

            // 車両タイプタブのイベント
            document.querySelectorAll('.car-type-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    // アクティブタブの更新
                    document.querySelectorAll('.car-type-tab').forEach(t => {
                        t.classList.remove('bg-blue-50', 'text-blue-700', 'border-b-2', 'border-blue-500', 'shadow-sm');
                        t.classList.add('text-gray-600');
                    });
                    
                    // 選択されたタブのスタイルを適用
                    this.classList.remove('text-gray-600');
                    this.classList.add('bg-blue-50', 'text-blue-700', 'border-b-2', 'border-blue-500', 'shadow-sm');

                    // 隠しフィールドの更新
                    document.getElementById('car_type').value = this.dataset.carType;
                    
                    // シーズン料金の更新
                    updateSeasonPrices(this.dataset.carType);
                });
            });

            // 年選択のイベントハンドラー
            document.getElementById('year').addEventListener('change', function() {
                updateMonthTabRestrictions();
            });

            // 初期表示時に現在選択されている車両タイプのシーズン料金を表示
            const initialCarType = document.getElementById('car_type').value;
            if (initialCarType) {
                updateSeasonPrices(initialCarType);
            }


            
            // 既存のシーズン料金を更新する関数
            function updateSeasonPrices(carType) {
                fetch(`/admin/car-type-prices/get-season-prices?car_type=${encodeURIComponent(carType)}`)
                    .then(response => response.json())
                    .then(data => {
                        // ハイシーズン料金を更新
                        const highSeasonInput = document.querySelector('input[name="season_prices[high_season][price_per_day]"]');
                        if (highSeasonInput && data.high_season) {
                            highSeasonInput.value = data.high_season.price_per_day;
                        }
                        
                        // 通常料金を更新
                        const normalInput = document.querySelector('input[name="season_prices[normal][price_per_day]"]');
                        if (normalInput && data.normal) {
                            normalInput.value = data.normal.price_per_day;
                        }
                        
                        // ローシーズン料金を更新
                        const lowSeasonInput = document.querySelector('input[name="season_prices[low_season][price_per_day]"]');
                        if (lowSeasonInput && data.low_season) {
                            lowSeasonInput.value = data.low_season.price_per_day;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching season prices:', error);
                    });
            }

            // 期間の重複チェック関数
            function checkPeriodOverlaps() {
                const periodRows = document.querySelectorAll('.period-row');
                const periods = [];
                
                periodRows.forEach((row, index) => {
                    const startDate = row.querySelector('input[name*="[start_date]"]').value;
                    const endDate = row.querySelector('input[name*="[end_date]"]').value;
                    
                    if (startDate && endDate) {
                        periods.push({
                            index: index + 1,
                            start: new Date(startDate),
                            end: new Date(endDate)
                        });
                    }
                });
                
                // 期間を開始日でソート
                periods.sort((a, b) => a.start - b.start);
                
                const overlaps = [];
                for (let i = 0; i < periods.length; i++) {
                    for (let j = i + 1; j < periods.length; j++) {
                        const period1 = periods[i];
                        const period2 = periods[j];
                        
                        // 期間が重複しているかチェック
                        if (period1.start <= period2.end && period1.end >= period2.start) {
                            overlaps.push(`期間${period1.index}と期間${period2.index}`);
                        }
                    }
                }
                
                return overlaps;
            }

            // フォーム送信時のバリデーション
            document.getElementById('yearlyForm').addEventListener('submit', function(e) {
                let hasError = false;
                let errorMessage = '';

                // シーズン料金の必須チェック
                const highSeasonPrice = document.querySelector('input[name="season_prices[high_season][price_per_day]"]').value;
                const normalPrice = document.querySelector('input[name="season_prices[normal][price_per_day]"]').value;
                const lowSeasonPrice = document.querySelector('input[name="season_prices[low_season][price_per_day]"]').value;

                if (!highSeasonPrice || highSeasonPrice < 1) {
                    errorMessage += 'ハイシーズン料金を入力してください。\n';
                    hasError = true;
                }

                if (!normalPrice || normalPrice < 1) {
                    errorMessage += '通常料金を入力してください。\n';
                    hasError = true;
                }

                if (!lowSeasonPrice || lowSeasonPrice < 1) {
                    errorMessage += 'ローシーズン料金を入力してください。\n';
                    hasError = true;
                }

                // シーズン料金が設定されている場合、期間設定が必須
                const hasSeasonPrices = (highSeasonPrice && highSeasonPrice >= 1) && 
                                      (normalPrice && normalPrice >= 1) && 
                                      (lowSeasonPrice && lowSeasonPrice >= 1);

                if (hasSeasonPrices) {
                    // 期間の必須チェック
                    const periodRows = document.querySelectorAll('.period-row');
                    if (periodRows.length === 0) {
                        errorMessage += 'シーズン料金を設定した場合は、少なくとも1つの期間設定が必要です。\n';
                        hasError = true;
                    } else {
                        periodRows.forEach((row, index) => {
                            const startDate = row.querySelector('input[name*="[start_date]"]').value;
                            const endDate = row.querySelector('input[name*="[end_date]"]').value;
                            const seasonType = row.querySelector('select[name*="[season_type]"]').value;

                            if (!startDate) {
                                errorMessage += `期間${index + 1}の開始日を入力してください。\n`;
                                hasError = true;
                            }

                            if (!endDate) {
                                errorMessage += `期間${index + 1}の終了日を入力してください。\n`;
                                hasError = true;
                            }

                            if (!seasonType) {
                                errorMessage += `期間${index + 1}の適用シーズンを選択してください。\n`;
                                hasError = true;
                            }
                        });
                    }
                }

                // 期間の重複チェック
                const overlaps = checkPeriodOverlaps();
                if (overlaps.length > 0) {
                    errorMessage += '期間が重複しています：\n' + overlaps.join('\n') + '\n';
                    hasError = true;
                }

                if (hasError) {
                    e.preventDefault();
                    alert('以下の項目を入力してください：\n' + errorMessage);
                    return false;
                }
            });
        });
    </script>
</x-admin-layout> 