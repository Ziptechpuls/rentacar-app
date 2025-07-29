<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('予約編集') }} - {{ $reservation->name_kanji }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.reservations.show', $reservation) }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                    戻る
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                入力に問題があります
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.reservations.update', $reservation) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- 車両情報 -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">車両情報</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">車両選択</label>
                                    <select name="car_id" id="car_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        @foreach(\App\Models\Car::with('carModel')->get() as $carOption)
                                            <option value="{{ $carOption->id }}" {{ $reservation->car_id == $carOption->id ? 'selected' : '' }}>
                                                {{ $carOption->carModel->name }} (ID: {{ $carOption->id }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">車種</label>
                                    <p class="mt-1 text-sm text-gray-900" id="car-model-name">{{ $reservation->car->carModel->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">料金（1日あたり）</label>
                                    <p class="mt-1 text-sm text-gray-900" id="car-price">¥{{ number_format($reservation->car->price) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- 顧客情報 -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">顧客情報</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="name_kanji" :value="__('氏名（漢字）')" />
                                    <x-text-input id="name_kanji" class="block mt-1 w-full" type="text" name="name_kanji" :value="old('name_kanji', $reservation->name_kanji)" required />
                                    <x-input-error :messages="$errors->get('name_kanji')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="name_kana_sei" :value="__('氏名（カナ姓）')" />
                                    <x-text-input id="name_kana_sei" class="block mt-1 w-full" type="text" name="name_kana_sei" :value="old('name_kana_sei', $reservation->name_kana_sei)" required />
                                    <x-input-error :messages="$errors->get('name_kana_sei')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="name_kana_mei" :value="__('氏名（カナ名）')" />
                                    <x-text-input id="name_kana_mei" class="block mt-1 w-full" type="text" name="name_kana_mei" :value="old('name_kana_mei', $reservation->name_kana_mei)" required />
                                    <x-input-error :messages="$errors->get('name_kana_mei')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="email" :value="__('メールアドレス')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $reservation->email)" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="phone_main" :value="__('電話番号（メイン）')" />
                                    <x-text-input id="phone_main" class="block mt-1 w-full" type="text" name="phone_main" :value="old('phone_main', $reservation->phone_main)" required />
                                    <x-input-error :messages="$errors->get('phone_main')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="phone_emergency" :value="__('緊急連絡先')" />
                                    <x-text-input id="phone_emergency" class="block mt-1 w-full" type="text" name="phone_emergency" :value="old('phone_emergency', $reservation->phone_emergency)" />
                                    <x-input-error :messages="$errors->get('phone_emergency')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- 予約期間 -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">予約期間</h3>
                            
                            <!-- 営業時間情報 -->
                            @php
                                $shop = \App\Models\Shop::first();
                                $businessHours = $shop ? $shop->business_hours : null;
                                $parsedBusinessHours = null;
                                if ($businessHours) {
                                    $pattern = '/(\d{1,2}):(\d{2})[〜\-~]\s*(\d{1,2}):(\d{2})/u';
                                    if (preg_match($pattern, $businessHours, $matches)) {
                                        $parsedBusinessHours = [
                                            'start' => sprintf('%02d:%02d', $matches[1], $matches[2]),
                                            'end' => sprintf('%02d:%02d', $matches[3], $matches[4])
                                        ];
                                    }
                                }
                            @endphp
                            
                            @if($parsedBusinessHours)
                                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <p class="text-sm text-blue-800">
                                        <strong>営業時間:</strong> {{ $parsedBusinessHours['start'] }}〜{{ $parsedBusinessHours['end'] }}
                                    </p>
                                </div>
                            @endif
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="start_date" :value="__('開始日')" />
                                    <x-text-input 
                                        id="start_date" 
                                        class="block mt-1 w-full" 
                                        type="text" 
                                        name="start_date" 
                                        :value="old('start_date', $reservation->start_datetime ? $reservation->start_datetime->format('Y-m-d') : '')" 
                                        required 
                                    />
                                    <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="end_date" :value="__('終了日')" />
                                    <x-text-input 
                                        id="end_date" 
                                        class="block mt-1 w-full" 
                                        type="text" 
                                        name="end_date" 
                                        :value="old('end_date', $reservation->end_datetime ? $reservation->end_datetime->format('Y-m-d') : '')" 
                                        required 
                                    />
                                    <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <x-input-label for="start_time" :value="__('開始時間')" />
                                    <select id="start_time" name="start_time" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        @for ($hour = 0; $hour < 24; $hour++)
                                            @for ($minute = 0; $minute < 60; $minute += 5)
                                                @php
                                                    $time = sprintf('%02d:%02d', $hour, $minute);
                                                    $selected = old('start_time', $reservation->start_datetime ? $reservation->start_datetime->format('H:i') : '08:00') == $time ? 'selected' : '';
                                                    
                                                    // 営業時間外かどうかをチェック
                                                    $isOutsideBusinessHours = false;
                                                    if ($parsedBusinessHours) {
                                                        $timeMinutes = $hour * 60 + $minute;
                                                        $startMinutes = (int)substr($parsedBusinessHours['start'], 0, 2) * 60 + (int)substr($parsedBusinessHours['start'], 3, 2);
                                                        $endMinutes = (int)substr($parsedBusinessHours['end'], 0, 2) * 60 + (int)substr($parsedBusinessHours['end'], 3, 2);
                                                        $isOutsideBusinessHours = $timeMinutes < $startMinutes || $timeMinutes > $endMinutes;
                                                    }
                                                @endphp
                                                @if (!$isOutsideBusinessHours)
                                                    <option value="{{ $time }}" {{ $selected }}>{{ $time }}</option>
                                                @endif
                                            @endfor
                                        @endfor
                                    </select>
                                    <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="end_time" :value="__('終了時間')" />
                                    <select id="end_time" name="end_time" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        @for ($hour = 0; $hour < 24; $hour++)
                                            @for ($minute = 0; $minute < 60; $minute += 5)
                                                @php
                                                    $time = sprintf('%02d:%02d', $hour, $minute);
                                                    $selected = old('end_time', $reservation->end_datetime ? $reservation->end_datetime->format('H:i') : '21:00') == $time ? 'selected' : '';
                                                    
                                                    // 営業時間外かどうかをチェック
                                                    $isOutsideBusinessHours = false;
                                                    if ($parsedBusinessHours) {
                                                        $timeMinutes = $hour * 60 + $minute;
                                                        $startMinutes = (int)substr($parsedBusinessHours['start'], 0, 2) * 60 + (int)substr($parsedBusinessHours['start'], 3, 2);
                                                        $endMinutes = (int)substr($parsedBusinessHours['end'], 0, 2) * 60 + (int)substr($parsedBusinessHours['end'], 3, 2);
                                                        $isOutsideBusinessHours = $timeMinutes < $startMinutes || $timeMinutes > $endMinutes;
                                                    }
                                                @endphp
                                                @if (!$isOutsideBusinessHours)
                                                    <option value="{{ $time }}" {{ $selected }}>{{ $time }}</option>
                                                @endif
                                            @endfor
                                        @endfor
                                    </select>
                                    <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
                                </div>
                            </div>
                            
                            <!-- 隠しフィールドでdatetime-local形式の値を送信 -->
                            <input type="hidden" name="start_datetime" id="start_datetime_hidden" value="{{ old('start_datetime', $reservation->start_datetime ? $reservation->start_datetime->format('Y-m-d\TH:i') : '') }}">
                            <input type="hidden" name="end_datetime" id="end_datetime_hidden" value="{{ old('end_datetime', $reservation->end_datetime ? $reservation->end_datetime->format('Y-m-d\TH:i') : '') }}">
                        </div>

                        <!-- 利用者数 -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">利用者数</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="number_of_adults" :value="__('大人の人数')" />
                                    <x-text-input id="number_of_adults" class="block mt-1 w-full" type="number" name="number_of_adults" :value="old('number_of_adults', $reservation->number_of_adults)" min="1" required />
                                    <x-input-error :messages="$errors->get('number_of_adults')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="number_of_children" :value="__('子供の人数')" />
                                    <x-text-input id="number_of_children" class="block mt-1 w-full" type="number" name="number_of_children" :value="old('number_of_children', $reservation->number_of_children)" min="0" />
                                    <x-input-error :messages="$errors->get('number_of_children')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- 料金設定 -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">料金設定</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="total_price" :value="__('総額')" />
                                    <x-text-input id="total_price" class="block mt-1 w-full" type="number" name="total_price" :value="old('total_price', $reservation->total_price)" min="0" step="100" required />
                                    <x-input-error :messages="$errors->get('total_price')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="status" :value="__('予約ステータス')" />
                                    <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="pending" {{ $reservation->status == 'pending' ? 'selected' : '' }}>保留</option>
                                        <option value="confirmed" {{ $reservation->status == 'confirmed' ? 'selected' : '' }}>確定</option>
                                        <option value="cancelled" {{ $reservation->status == 'cancelled' ? 'selected' : '' }}>キャンセル</option>
                                        <option value="completed" {{ $reservation->status == 'completed' ? 'selected' : '' }}>完了</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- 備考 -->
                        <div>
                            <x-input-label for="notes" :value="__('備考')" />
                            <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $reservation->notes) }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <!-- 送信ボタン -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('admin.reservations.show', $reservation) }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                                キャンセル
                            </a>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                                更新する
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const startDateEl = document.getElementById('start_date');
                const endDateEl = document.getElementById('end_date');
                const startTimeEl = document.getElementById('start_time');
                const endTimeEl = document.getElementById('end_time');
                const startDatetimeHidden = document.getElementById('start_datetime_hidden');
                const endDatetimeHidden = document.getElementById('end_datetime_hidden');

                // Litepickerの初期化
                const startPicker = new Litepicker({
                    element: startDateEl,
                    format: 'YYYY-MM-DD',
                    minDate: new Date(),
                    setup: (picker) => {
                        picker.on('selected', () => updateEndConstraints());
                    }
                });

                const endPicker = new Litepicker({
                    element: endDateEl,
                    format: 'YYYY-MM-DD',
                    minDate: new Date(),
                    setup: (picker) => {
                        picker.on('selected', () => validateEndTime());
                    }
                });

                [startDateEl, startTimeEl].forEach(el => {
                    el.addEventListener('change', updateEndConstraints);
                });

                [endDateEl, endTimeEl].forEach(el => {
                    el.addEventListener('change', validateEndTime);
                });

                function updateEndConstraints() {
                    const startDate = startDateEl.value;
                    if (startDate) {
                        endPicker.setOptions({ minDate: startDate });
                    }
                    validateEndTime();
                }

                function validateEndTime() {
                    const startDate = startDateEl.value;
                    const startTime = startTimeEl.value;
                    const endDate = endDateEl.value;
                    const endTime = endTimeEl.value;

                    if (!startDate || !startTime || !endDate || !endTime) return;

                    const start = new Date(`${startDate}T${startTime}`);
                    const end = new Date(`${endDate}T${endTime}`);

                    if (end < start) {
                        alert('終了日時は開始日時より後の時間を選択してください。');
                        endTimeEl.value = '';
                        endTimeEl.classList.add('border-red-500');
                        endTimeEl.title = '開始日時より後の時刻を選んでください';
                    } else {
                        endTimeEl.classList.remove('border-red-500');
                        endTimeEl.title = '';
                    }
                }

                // 営業時間の制限を適用（営業時間外のオプションを削除）
                function applyBusinessHoursRestriction() {
                    const businessStartTime = '{{ $parsedBusinessHours["start"] ?? "08:00" }}';
                    const businessEndTime = '{{ $parsedBusinessHours["end"] ?? "21:00" }}';
                    
                    // 利用開始時間の制限
                    Array.from(startTimeEl.options).forEach(option => {
                        const optionTime = option.value;
                        const optionMinutes = parseInt(optionTime.split(':')[0]) * 60 + parseInt(optionTime.split(':')[1]);
                        const businessStartMinutes = parseInt(businessStartTime.split(':')[0]) * 60 + parseInt(businessStartTime.split(':')[1]);
                        const businessEndMinutes = parseInt(businessEndTime.split(':')[0]) * 60 + parseInt(businessEndTime.split(':')[1]);
                        
                        if (optionMinutes < businessStartMinutes || optionMinutes > businessEndMinutes) {
                            option.remove();
                        }
                    });
                    
                    // 利用終了時間の制限
                    Array.from(endTimeEl.options).forEach(option => {
                        const optionTime = option.value;
                        const optionMinutes = parseInt(optionTime.split(':')[0]) * 60 + parseInt(optionTime.split(':')[1]);
                        const businessStartMinutes = parseInt(businessStartTime.split(':')[0]) * 60 + parseInt(businessStartTime.split(':')[1]);
                        const businessEndMinutes = parseInt(businessEndTime.split(':')[0]) * 60 + parseInt(businessEndTime.split(':')[1]);
                        
                        if (optionMinutes < businessStartMinutes || optionMinutes > businessEndMinutes) {
                            option.remove();
                        }
                    });
                }

                // 日付と時間が変更されたときに隠しフィールドを更新
                function updateHiddenFields() {
                    const startDate = startDateEl.value;
                    const endDate = endDateEl.value;
                    const startTime = startTimeEl.value;
                    const endTime = endTimeEl.value;
                    
                    if (startDate && startTime) {
                        startDatetimeHidden.value = startDate + 'T' + startTime;
                    }
                    
                    if (endDate && endTime) {
                        endDatetimeHidden.value = endDate + 'T' + endTime;
                    }
                }
                
                // 各フィールドの変更を監視
                startDateEl.addEventListener('change', updateHiddenFields);
                endDateEl.addEventListener('change', updateHiddenFields);
                startTimeEl.addEventListener('change', updateHiddenFields);
                endTimeEl.addEventListener('change', updateHiddenFields);
                
                // 初期化
                applyBusinessHoursRestriction();
                updateHiddenFields();
            });
        </script>
    @endpush
</x-admin-layout> 