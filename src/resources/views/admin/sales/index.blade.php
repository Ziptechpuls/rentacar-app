<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('売上管理') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 期間フィルター -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">期間フィルター</h3>
                    <form method="GET" class="flex gap-4 items-end">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">開始日</label>
                            <input type="date" name="start_date" id="start_date" 
                                   value="{{ $startDate->format('Y-m-d') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">終了日</label>
                            <input type="date" name="end_date" id="end_date" 
                                   value="{{ $endDate->format('Y-m-d') }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            フィルター適用
                        </button>
                    </form>
                </div>
            </div>

            <!-- 統計カード -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- 総売上 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">総売上</p>
                                <p class="text-2xl font-semibold text-gray-900">¥{{ number_format($totalRevenue) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 今月の売上 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">今月の売上</p>
                                <p class="text-2xl font-semibold text-gray-900">¥{{ number_format($monthlyRevenue) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 今月の予約数 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">今月の予約数</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($monthlyReservations) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 平均単価 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">平均単価</p>
                                <p class="text-2xl font-semibold text-gray-900">¥{{ number_format($averagePrice) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- グラフセクション -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- 月別売上推移 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">月別売上推移</h3>
                        <div style="height: 300px;">
                            <canvas id="monthlySalesChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- 日別売上 -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">日別売上（過去30日）</h3>
                        <div style="height: 300px;">
                            <canvas id="dailySalesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 車種別売上 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">車種別売上</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div style="height: 400px;">
                            <canvas id="carModelSalesChart"></canvas>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">車種</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">売上</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">構成比</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($carModelSales as $model)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $model->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                                ¥{{ number_format($model->total_sales) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                                {{ $model->percentage }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 利益管理 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">利益管理</h3>
                    
                    <!-- 利益サマリー -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-green-600">総売上</div>
                            <div class="text-2xl font-bold text-green-700">¥{{ number_format($totalRevenue) }}</div>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-red-600">総経費</div>
                            <div class="text-2xl font-bold text-red-700">¥{{ number_format($totalExpenses) }}</div>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-blue-600">粗利</div>
                            <div class="text-2xl font-bold text-blue-700">¥{{ number_format($grossProfit) }}</div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <div class="text-sm font-medium text-purple-600">純利益</div>
                            <div class="text-2xl font-bold text-purple-700">¥{{ number_format($netProfit) }}</div>
                        </div>
                    </div>

                    <!-- 経費入力フォーム -->
                    <div class="mt-6">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">経費入力</h4>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- 固定費 -->
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h5 class="text-md font-medium text-gray-900 mb-4">固定費</h5>
                                <div class="space-y-4">
                                    @foreach($fixedExpenses as $index => $expense)
                                        <div class="flex items-center">
                                            <label class="text-sm font-medium text-gray-700 w-32 flex-shrink-0">{{ $expense['name'] }}</label>
                                            <div class="flex-1 ml-4">
                                                <input type="text" 
                                                       id="fixed_expense_{{ $index }}"
                                                       class="fixed-expense w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2"
                                                       placeholder="0"
                                                       value="{{ number_format($expense['amount']) }}"
                                                       data-category-id="{{ $expense['id'] }}"
                                                       onchange="updateExpenses()"
                                                       onblur="formatNumber(this)"
                                                       onfocus="removeCommas(this)">
                                            </div>
                                            <span class="text-sm text-gray-500 ml-2 flex-shrink-0">円</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- 変動費 -->
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h5 class="text-md font-medium text-gray-900 mb-4">変動費</h5>
                                <div class="space-y-4">
                                    @foreach($variableExpenses as $index => $expense)
                                        <div class="flex items-center">
                                            <label class="text-sm font-medium text-gray-700 w-32 flex-shrink-0">{{ $expense['name'] }}</label>
                                            <div class="flex-1 ml-4">
                                                <input type="text" 
                                                       id="variable_expense_{{ $index }}"
                                                       class="variable-expense w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm px-3 py-2"
                                                       placeholder="0"
                                                       value="{{ number_format($expense['amount']) }}"
                                                       data-category-id="{{ $expense['id'] }}"
                                                       onchange="updateExpenses()"
                                                       onblur="formatNumber(this)"
                                                       onfocus="removeCommas(this)">
                                            </div>
                                            <span class="text-sm text-gray-500 ml-2 flex-shrink-0">円</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 経費項目管理 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">経費項目管理</h3>
                    
                    @if(session('success'))
                        <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- 新規追加フォーム -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-md font-medium text-gray-900 mb-3">新規経費項目追加</h4>
                        <form method="POST" action="{{ route('admin.expense-categories.store') }}" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">項目名</label>
                                    <input type="text" name="name" id="name" required
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700">種別</label>
                                    <select name="type" id="type" required
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="fixed">固定費</option>
                                        <option value="variable">変動費</option>
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <button type="submit" 
                                            class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                        追加
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- 経費項目一覧 -->
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-3">経費項目一覧</h4>
                        
                        @php
                            $companyId = auth('admin')->user()->company_id;
                            $categories = \App\Models\ExpenseCategory::where('company_id', $companyId)
                                ->orderBy('sort_order')
                                ->orderBy('name')
                                ->get();
                        @endphp
                        
                        @if($categories->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">項目名</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">種別</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">状態</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($categories as $category)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $category->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                        @if($category->type === 'fixed') bg-blue-100 text-blue-800
                                                        @else bg-green-100 text-green-800 @endif">
                                                        {{ $category->type_name }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                        @if($category->is_active) bg-green-100 text-green-800
                                                        @else bg-red-100 text-red-800 @endif">
                                                        {{ $category->is_active ? '有効' : '無効' }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div class="flex justify-end space-x-2">
                                                        <button onclick="editCategory({{ $category->id }}, '{{ $category->name }}', '{{ $category->type }}')"
                                                                class="text-indigo-600 hover:text-indigo-900">
                                                            編集
                                                        </button>
                                                        <form method="POST" action="{{ route('admin.expense-categories.toggle-active', $category) }}" class="inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" 
                                                                    class="text-yellow-600 hover:text-yellow-900">
                                                                {{ $category->is_active ? '無効化' : '有効化' }}
                                                            </button>
                                                        </form>
                                                        <form method="POST" action="{{ route('admin.expense-categories.destroy', $category) }}" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    onclick="return confirm('本当に削除しますか？')"
                                                                    class="text-red-600 hover:text-red-900">
                                                                削除
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-4">経費項目がありません。</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 編集モーダル -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">経費項目編集</h3>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label for="edit_name" class="block text-sm font-medium text-gray-700">項目名</label>
                            <input type="text" name="name" id="edit_name" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label for="edit_type" class="block text-sm font-medium text-gray-700">種別</label>
                            <select name="type" id="edit_type" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="fixed">固定費</option>
                                <option value="variable">変動費</option>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeEditModal()"
                                    class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                                キャンセル
                            </button>
                            <button type="submit"
                                    class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                更新
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // 月別売上推移グラフ
        const monthlyCtx = document.getElementById('monthlySalesChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: @json(collect($monthlySalesData)->pluck('label')),
                datasets: [{
                    label: '月別売上',
                    data: @json(collect($monthlySalesData)->pluck('sales')),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1,
                    borderWidth: 3,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '¥' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // 車種別売上グラフ
        const carModelCtx = document.getElementById('carModelSalesChart').getContext('2d');
        new Chart(carModelCtx, {
            type: 'doughnut',
            data: {
                labels: @json($carModelSales->pluck('name')),
                datasets: [{
                    data: @json($carModelSales->pluck('total_sales')),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(199, 199, 199, 0.8)',
                        'rgba(83, 102, 255, 0.8)'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });

        // 日別売上グラフ
        const dailyCtx = document.getElementById('dailySalesChart').getContext('2d');
        new Chart(dailyCtx, {
            type: 'bar',
            data: {
                labels: @json($dailySalesData->pluck('label')),
                datasets: [{
                    label: '売上',
                    data: @json($dailySalesData->pluck('sales')),
                    backgroundColor: 'rgba(79, 70, 229, 0.2)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '¥' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // 経費計算のリアルタイム更新
        function updateExpenses() {
            let totalFixedExpenses = 0;
            let totalVariableExpenses = 0;
            
            // 固定費の合計を計算
            document.querySelectorAll('.fixed-expense').forEach(function(input) {
                const value = parseFloat(input.value.replace(/,/g, '')) || 0;
                totalFixedExpenses += value;
                
                // 金額を保存
                if (value > 0) {
                    saveExpenseAmount(input.dataset.categoryId, value);
                }
            });
            
            // 変動費の合計を計算
            document.querySelectorAll('.variable-expense').forEach(function(input) {
                const value = parseFloat(input.value.replace(/,/g, '')) || 0;
                totalVariableExpenses += value;
                
                // 金額を保存
                if (value > 0) {
                    saveExpenseAmount(input.dataset.categoryId, value);
                }
            });
            
            const totalExpenses = totalFixedExpenses + totalVariableExpenses;
            const totalRevenue = {{ $totalRevenue }};
            const grossProfit = totalRevenue - totalExpenses;
            const netProfit = grossProfit;
            
            // 表示を更新
            document.querySelector('.bg-red-50 .text-2xl').textContent = '¥' + totalExpenses.toLocaleString();
            document.querySelector('.bg-blue-50 .text-2xl').textContent = '¥' + grossProfit.toLocaleString();
            document.querySelector('.bg-purple-50 .text-2xl').textContent = '¥' + netProfit.toLocaleString();
        }

        function saveExpenseAmount(categoryId, amount) {
            fetch('{{ route("admin.expense-amounts.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    expense_category_id: categoryId,
                    amount: amount
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('経費金額を保存しました');
                }
            })
            .catch(error => {
                console.error('エラー:', error);
            });
        }

        // カンマを除去する関数
        function removeCommas(input) {
            input.value = input.value.replace(/,/g, '');
        }

        // カンマを追加する関数
        function formatNumber(input) {
            let value = input.value.replace(/,/g, '');
            if (value === '') return;
            
            const numValue = parseInt(value);
            if (!isNaN(numValue)) {
                input.value = numValue.toLocaleString();
            }
        }

        // 経費項目編集モーダル
        function editCategory(id, name, type) {
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_type').value = type;
            document.getElementById('editForm').action = `/admin/expense-categories/${id}`;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</x-admin-layout> 