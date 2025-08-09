<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('予約追加') }} - {{ $car->carModel->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.cars.show', $car) }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
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
                    <form method="POST" action="{{ route('admin.reservations.store') }}" class="space-y-6">
                        @csrf
                        <input type="hidden" name="car_id" value="{{ $car->id }}">

                        <!-- 車両情報 -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">車両情報</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">車両選択</label>
                                    <select name="car_id" id="car_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        @foreach(\App\Models\Car::with('carModel')->get() as $carOption)
                                            <option value="{{ $carOption->id }}" {{ $car->id == $carOption->id ? 'selected' : '' }}>
                                                {{ $carOption->carModel->name }} (ID: {{ $carOption->id }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">車両タイプ</label>
                                    <p class="mt-1 text-sm text-gray-900" id="car-model-name">{{ $car->carModel->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">料金（1日あたり）</label>
                                    <p class="mt-1 text-sm text-gray-900" id="car-price">¥{{ number_format($car->price) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- 顧客情報 -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">顧客情報</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="name_kanji" :value="__('氏名（漢字）')" />
                                    <x-text-input id="name_kanji" class="block mt-1 w-full" type="text" name="name_kanji" :value="old('name_kanji')" required />
                                    <x-input-error :messages="$errors->get('name_kanji')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="name_kana_sei" :value="__('氏名（カナ姓）')" />
                                    <x-text-input id="name_kana_sei" class="block mt-1 w-full" type="text" name="name_kana_sei" :value="old('name_kana_sei')" required />
                                    <x-input-error :messages="$errors->get('name_kana_sei')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="name_kana_mei" :value="__('氏名（カナ名）')" />
                                    <x-text-input id="name_kana_mei" class="block mt-1 w-full" type="text" name="name_kana_mei" :value="old('name_kana_mei')" required />
                                    <x-input-error :messages="$errors->get('name_kana_mei')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="email" :value="__('メールアドレス')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="phone_main" :value="__('電話番号（メイン）')" />
                                    <x-text-input id="phone_main" class="block mt-1 w-full" type="text" name="phone_main" :value="old('phone_main')" required />
                                    <x-input-error :messages="$errors->get('phone_main')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="phone_emergency" :value="__('電話番号（緊急時）')" />
                                    <x-text-input id="phone_emergency" class="block mt-1 w-full" type="text" name="phone_emergency" :value="old('phone_emergency')" />
                                    <x-input-error :messages="$errors->get('phone_emergency')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- 予約期間 -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">予約期間</h3>
                            
                            <!-- 営業時間情報 -->
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
                                        :value="old('start_date', date('Y-m-d'))" 
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
                                        :value="old('end_date', date('Y-m-d'))" 
                                        required 
                                    />
                                    <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <x-input-label for="start_time" :value="__('開始時間')" />
                                    <input type="time" id="start_time" name="start_time" 
                                           value="{{ old('start_time', '08:00') }}"
                                           min="{{ $parsedBusinessHours['start'] ?? '08:00' }}"
                                           max="{{ $parsedBusinessHours['end'] ?? '21:00' }}"
                                           step="300"
                                           class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                           required>
                                    <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="end_time" :value="__('終了時間')" />
                                    <input type="time" id="end_time" name="end_time" 
                                           value="{{ old('end_time', '21:00') }}"
                                           min="{{ $parsedBusinessHours['start'] ?? '08:00' }}"
                                           max="{{ $parsedBusinessHours['end'] ?? '21:00' }}"
                                           step="300"
                                           class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                                           required>
                                    <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
                                </div>
                            </div>
                            
                            <!-- 隠しフィールドでdatetime-local形式の値を送信 -->
                            <input type="hidden" name="start_datetime" id="start_datetime_hidden">
                            <input type="hidden" name="end_datetime" id="end_datetime_hidden">
                            
                            @if($errors->has('business_hours'))
                                <div class="mt-2">
                                    <x-input-error :messages="$errors->get('business_hours')" class="mt-2" />
                                </div>
                            @endif
                        </div>

                        <!-- 利用者数 -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">利用者数</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="number_of_adults" :value="__('大人の人数')" />
                                    <x-text-input id="number_of_adults" class="block mt-1 w-full" type="number" name="number_of_adults" :value="old('number_of_adults', 1)" min="1" max="10" required />
                                    <x-input-error :messages="$errors->get('number_of_adults')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="number_of_children" :value="__('子供の人数')" />
                                    <x-text-input id="number_of_children" class="block mt-1 w-full" type="number" name="number_of_children" :value="old('number_of_children', 0)" min="0" max="10" required />
                                    <x-input-error :messages="$errors->get('number_of_children')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- フライト情報 -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">フライト情報</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="flight_number_arrival" :value="__('到着便')" />
                                    <x-text-input id="flight_number_arrival" class="block mt-1 w-full" type="text" name="flight_number_arrival" :value="old('flight_number_arrival')" />
                                    <x-input-error :messages="$errors->get('flight_number_arrival')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="flight_number_departure" :value="__('出発便')" />
                                    <x-text-input id="flight_number_departure" class="block mt-1 w-full" type="text" name="flight_number_departure" :value="old('flight_number_departure')" />
                                    <x-input-error :messages="$errors->get('flight_number_departure')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="flight_departure" :value="__('出発空港')" />
                                    <x-text-input id="flight_departure" class="block mt-1 w-full" type="text" name="flight_departure" :value="old('flight_departure')" />
                                    <x-input-error :messages="$errors->get('flight_departure')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="flight_return" :value="__('到着空港')" />
                                    <x-text-input id="flight_return" class="block mt-1 w-full" type="text" name="flight_return" :value="old('flight_return')" />
                                    <x-input-error :messages="$errors->get('flight_return')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- 料金計算 -->
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">料金計算</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">1日あたりの料金</label>
                                    <p class="mt-1 text-lg font-semibold text-gray-900">¥{{ number_format($car->price) }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">予約日数</label>
                                    <p class="mt-1 text-lg font-semibold text-gray-900" id="days-count">計算中...</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">合計料金</label>
                                    <p class="mt-1 text-lg font-semibold text-blue-600" id="total-price">計算中...</p>
                                </div>
                            </div>
                            <input type="hidden" name="total_price" id="total_price_input" value="{{ $car->price }}">
                        </div>

                        <!-- 備考 -->
                        <div>
                            <x-input-label for="notes" :value="__('備考')" />
                            <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <!-- ステータス -->
                        <div>
                            <x-input-label for="status" :value="__('ステータス')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>保留</option>
                                <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>確定</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>キャンセル</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button class="ml-3">
                                {{ __('予約を追加') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // 営業時間の設定
        const businessHours = @json($businessHours);
        const defaultTimes = @json($defaultTimes);
        
        // 営業時間を解析する関数
        function parseBusinessHours(hours) {
            if (!hours) return null;
            
            // 複数のパターンに対応
            const patterns = [
                /(\d{1,2}):(\d{2})[〜\-~]\s*(\d{1,2}):(\d{2})/u,  // 8:00〜21:00
                /(\d{1,2}):(\d{2})[〜\-~]\s*(\d{1,2}):(\d{2})\s*\(最終送迎(\d{1,2}):(\d{2})\)/u  // 8:00〜21:00 (最終送迎18:00)
            ];
            
            for (const pattern of patterns) {
                const match = hours.match(pattern);
                if (match) {
                    const openHour = parseInt(match[1]);
                    const openMinute = parseInt(match[2]);
                    const closeHour = parseInt(match[3]);
                    const closeMinute = parseInt(match[4]);
                    
                    // 最終送迎時間がある場合はそれを使用
                    let finalPickupHour = closeHour;
                    let finalPickupMinute = closeMinute;
                    
                    if (match[5] && match[6]) {
                        finalPickupHour = parseInt(match[5]);
                        finalPickupMinute = parseInt(match[6]);
                    }
                    
                    return {
                        open: { hour: openHour, minute: openMinute },
                        close: { hour: closeHour, minute: closeMinute },
                        finalPickup: { hour: finalPickupHour, minute: finalPickupMinute }
                    };
                }
            }
            return null;
        }
        
        // 営業時間内かチェックする関数
        function isWithinBusinessHours(date) {
            const parsed = parseBusinessHours(businessHours);
            if (!parsed) return true; // 営業時間が設定されていない場合は制限なし
            
            const hour = date.getHours();
            const minute = date.getMinutes();
            const timeInMinutes = hour * 60 + minute;
            const openInMinutes = parsed.open.hour * 60 + parsed.open.minute;
            const closeInMinutes = parsed.close.hour * 60 + parsed.close.minute;
            
            // 開始時間は営業開始時間以降、終了時間は営業終了時間まで
            return timeInMinutes >= openInMinutes && timeInMinutes <= closeInMinutes;
        }
        
        // 営業時間の詳細を取得する関数
        function getBusinessHoursInfo() {
            const parsed = parseBusinessHours(businessHours);
            if (!parsed) return null;
            
            return {
                open: `${parsed.open.hour.toString().padStart(2, '0')}:${parsed.open.minute.toString().padStart(2, '0')}`,
                close: `${parsed.close.hour.toString().padStart(2, '0')}:${parsed.close.minute.toString().padStart(2, '0')}`
            };
        }
        
        // 時間選択の制限を設定
        function setupTimeRestrictions() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const startTimeInput = document.getElementById('start_time');
            const endTimeInput = document.getElementById('end_time');
            const startDatetimeHidden = document.getElementById('start_datetime_hidden');
            const endDatetimeHidden = document.getElementById('end_datetime_hidden');
            const businessInfo = getBusinessHoursInfo();
            
            if (businessInfo) {
                // 営業時間の情報を表示
                console.log('営業時間:', businessInfo);
            }
            
            // 営業時間の制限を適用（営業時間外のオプションを削除）
            function applyBusinessHoursRestriction() {
                const businessStartTime = '{{ $parsedBusinessHours["start"] ?? "08:00" }}';
                const businessEndTime = '{{ $parsedBusinessHours["end"] ?? "21:00" }}';
                
                // 利用開始時間の制限
                Array.from(startTimeInput.options).forEach(option => {
                    const optionTime = option.value;
                    const optionMinutes = parseInt(optionTime.split(':')[0]) * 60 + parseInt(optionTime.split(':')[1]);
                    const businessStartMinutes = parseInt(businessStartTime.split(':')[0]) * 60 + parseInt(businessStartTime.split(':')[1]);
                    const businessEndMinutes = parseInt(businessEndTime.split(':')[0]) * 60 + parseInt(businessEndTime.split(':')[1]);
                    
                    if (optionMinutes < businessStartMinutes || optionMinutes > businessEndMinutes) {
                        option.remove();
                    }
                });
                
                // 利用終了時間の制限
                Array.from(endTimeInput.options).forEach(option => {
                    const optionTime = option.value;
                    const optionMinutes = parseInt(optionTime.split(':')[0]) * 60 + parseInt(optionTime.split(':')[1]);
                    const businessStartMinutes = parseInt(businessStartTime.split(':')[0]) * 60 + parseInt(businessStartTime.split(':')[1]);
                    const businessEndMinutes = parseInt(businessEndTime.split(':')[0]) * 60 + parseInt(businessEndTime.split(':')[1]);
                    
                    if (optionMinutes < businessStartMinutes || optionMinutes > businessEndMinutes) {
                        option.remove();
                    }
                });
            }
            
            // 営業時間制限を適用
            applyBusinessHoursRestriction();
            
            // 日付と時間が変更されたときに隠しフィールドを更新
            function updateHiddenFields() {
                const startDate = startDateInput.value;
                const endDate = endDateInput.value;
                const startTime = startTimeInput.value;
                const endTime = endTimeInput.value;
                
                if (startDate && startTime) {
                    startDatetimeHidden.value = startDate + 'T' + startTime;
                    console.log('開始日時:', startDatetimeHidden.value);
                }
                
                if (endDate && endTime) {
                    endDatetimeHidden.value = endDate + 'T' + endTime;
                    console.log('終了日時:', endDatetimeHidden.value);
                }
                
                calculatePrice();
            }
            
            // 各フィールドの変更を監視
            startDateInput.addEventListener('change', updateHiddenFields);
            endDateInput.addEventListener('change', updateHiddenFields);
            startTimeInput.addEventListener('change', updateHiddenFields);
            endTimeInput.addEventListener('change', updateHiddenFields);
            
            // 初期値を設定
            updateHiddenFields();
        }
        
        // 車両選択時の処理
        document.getElementById('car_id').addEventListener('change', function() {
            const carId = this.value;
            const cars = @json(\App\Models\Car::with('carModel')->get());
            const selectedCar = cars.find(car => car.id == carId);
            
            if (selectedCar) {
                document.getElementById('car-model-name').textContent = selectedCar.car_model.name;
                document.getElementById('car-price').textContent = '¥' + selectedCar.price.toLocaleString();
                
                // 料金を再計算
                calculatePrice();
            }
        });

        // 料金計算のJavaScript
        function calculatePrice() {
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            const startTimeInput = document.getElementById('start_time');
            const endTimeInput = document.getElementById('end_time');
            
            const startDate = new Date(startDateInput.value + 'T' + startTimeInput.value);
            const endDate = new Date(endDateInput.value + 'T' + endTimeInput.value);
            const carId = document.getElementById('car_id').value;
            const cars = @json(\App\Models\Car::with('carModel')->get());
            const selectedCar = cars.find(car => car.id == carId);
            const dailyPrice = selectedCar ? selectedCar.price : {{ $car->price }};
            
            if (startDate && endDate && startDate < endDate) {
                const diffTime = Math.abs(endDate - startDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                const totalPrice = diffDays * dailyPrice;
                
                document.getElementById('days-count').textContent = diffDays + '日';
                document.getElementById('total-price').textContent = '¥' + totalPrice.toLocaleString();
                document.getElementById('total_price_input').value = totalPrice;
            } else {
                document.getElementById('days-count').textContent = '日数を入力してください';
                document.getElementById('total-price').textContent = '料金を計算できません';
                document.getElementById('total_price_input').value = dailyPrice;
            }
        }

        // ページ読み込み時に初期化
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

            // HTML5 time input用の営業時間制限
            function setupTimeRestrictions() {
                const businessStartTime = '{{ $parsedBusinessHours["start"] ?? "08:00" }}';
                const businessEndTime = '{{ $parsedBusinessHours["end"] ?? "21:00" }}';
                
                // 時間入力の制限を設定
                startTimeEl.min = businessStartTime;
                startTimeEl.max = businessEndTime;
                endTimeEl.min = businessStartTime;
                endTimeEl.max = businessEndTime;
                
                // 時間が変更されたときのバリデーション
                startTimeEl.addEventListener('change', function() {
                    const selectedTime = this.value;
                    if (selectedTime < businessStartTime || selectedTime > businessEndTime) {
                        alert('営業時間内の時間を選択してください。');
                        this.value = businessStartTime;
                    }
                    updateHiddenFields();
                    validateEndTime();
                });
                
                endTimeEl.addEventListener('change', function() {
                    const selectedTime = this.value;
                    if (selectedTime < businessStartTime || selectedTime > businessEndTime) {
                        alert('営業時間内の時間を選択してください。');
                        this.value = businessEndTime;
                    }
                    updateHiddenFields();
                    validateEndTime();
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
            setupTimeRestrictions(); // 時間制限の初期化を追加
            updateHiddenFields();
            
            // 日時が変更されたときに料金を再計算
            document.getElementById('start_date').addEventListener('change', calculatePrice);
            document.getElementById('end_date').addEventListener('change', calculatePrice);
            document.getElementById('start_time').addEventListener('change', calculatePrice);
            document.getElementById('end_time').addEventListener('change', calculatePrice);
        });
    </script>
</x-admin-layout> 