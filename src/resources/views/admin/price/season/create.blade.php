<x-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('シーズン設定の追加') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.price.season.store') }}" method="POST">
                        @csrf
                        <div class="space-y-6">
                            {{-- 基本情報 --}}
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">基本情報</h3>
                                <div class="grid grid-cols-1 gap-6">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">シーズン名</label>
                                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            required>
                                    </div>
                                    <div>
                                        <label for="description" class="block text-sm font-medium text-gray-700">説明</label>
                                        <textarea name="description" id="description" rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            required>{{ old('description') }}</textarea>
                                    </div>
                                    <div>
                                        <label for="rate" class="block text-sm font-medium text-gray-700">料金倍率（%）</label>
                                        <input type="number" name="rate" id="rate" value="{{ old('rate', 100) }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                            min="1" max="200" required>
                                        <p class="mt-1 text-sm text-gray-500">100% = 通常料金、80% = 20%引き、120% = 20%増</p>
                                    </div>
                                </div>
                            </div>

                            {{-- 期間設定 --}}
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">期間設定</h3>
                                <div id="periods-container" class="space-y-4">
                                    <div class="period-row grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">開始日</label>
                                            <input type="date" name="periods[0][start_date]"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">終了日</label>
                                            <input type="date" name="periods[0][end_date]"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                required>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" onclick="addPeriod()"
                                    class="mt-4 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    + 期間を追加
                                </button>
                            </div>

                            <div class="flex justify-end space-x-4">
                                <button type="button" onclick="location.href='{{ route('admin.price.index') }}'"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    キャンセル
                                </button>
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    保存する
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let periodCount = 1;

        function addPeriod() {
            const container = document.getElementById('periods-container');
            const newRow = document.createElement('div');
            newRow.className = 'period-row grid grid-cols-2 gap-4';
            newRow.innerHTML = `
                <div>
                    <label class="block text-sm font-medium text-gray-700">開始日</label>
                    <input type="date" name="periods[${periodCount}][start_date]"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        required>
                </div>
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700">終了日</label>
                    <input type="date" name="periods[${periodCount}][end_date]"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        required>
                    <button type="button" onclick="removePeriod(this)" class="absolute right-0 top-8 text-red-600 hover:text-red-800">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            `;
            container.appendChild(newRow);
            periodCount++;
        }

        function removePeriod(button) {
            button.closest('.period-row').remove();
        }
    </script>
    @endpush
</x-admin-layout> 