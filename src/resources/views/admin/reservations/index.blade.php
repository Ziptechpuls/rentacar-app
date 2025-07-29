<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('予約管理') }}
            </h2>
            <div class="flex space-x-2">
                <button id="today-btn" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                    今日
                </button>
                <button id="prev-btn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    ← 前月
                </button>
                <button id="next-btn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    翌月 →
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">予約管理</h2>
                        <div class="text-sm text-gray-600">
                            <div class="flex items-center space-x-6">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 legend-confirmed rounded mr-3"></div>
                                    <span>確認済み予約</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-4 h-4 legend-pending rounded mr-3"></div>
                                    <span>保留中予約</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 予約期間の説明 -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <h3 class="text-sm font-semibold text-blue-800 mb-2">予約期間の表示について</h3>
                        <div class="text-sm text-blue-700 space-y-1">
                            <p>• <span class="font-medium">青色</span>: 確認済み予約（確定済み）</p>
                            <p>• <span class="font-medium">黄色</span>: 保留中予約（確認待ち）</p>
                            <p>• 予約期間は開始日から終了日まで同じ色で表示されます</p>
                            <p>• 空いている日には「予約追加」ボタンが表示されます</p>
                            <p>• 各日付の下に <span class="font-medium text-green-600">🚗出発件数</span> と <span class="font-medium text-blue-600">🏁返却件数</span> を表示</p>
                            <p>• 「予約追加」ボタンから月跨ぎ予約も自由に作成可能</p>
                            <p>• <span class="font-medium text-red-600">🔧車検最終日</span> の赤いマークで車検日をお知らせ</p>
                        </div>
                    </div>

            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
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

            <!-- 月表示ヘッダー -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900" id="month-display">
                            {{ Carbon\Carbon::today()->format('Y年m月') }}
                        </h3>
                        <div class="text-sm text-gray-500">
                            総予約数: <span class="font-semibold">{{ $reservations->total() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 車両×カレンダーグリッド -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200" id="reservation-grid">
                        <thead class="bg-gray-50 sticky-header">
                            <tr>
                                <th class="sticky left-0 z-10 bg-gray-50 border-r border-gray-200 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-64 sticky-car-info">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        車両情報
                                    </div>
                                </th>
                                @php
                                    // 今日の日付を基準に表示範囲を設定
                                    $today = Carbon\Carbon::today();
                                    $currentDate = $today->copy()->subMonth();
                                    $endDate = $today->copy()->addMonth();
                                @endphp
                                @while($currentDate <= $endDate)
                                    @php
                                        // その日の出発件数（予約開始件数）
                                        $currentDateString = $currentDate->format('Y-m-d');
                                        
                                        $departureCount = $cars->sum(function($car) use ($currentDateString) {
                                            return $car->reservations->where('status', '!=', 'cancelled')
                                                ->filter(function($reservation) use ($currentDateString) {
                                                    return $reservation->start_datetime->format('Y-m-d') === $currentDateString;
                                                })
                                                ->count();
                                        });
                                        
                                        // その日の返却件数（予約終了件数）
                                        $returnCount = $cars->sum(function($car) use ($currentDateString) {
                                            return $car->reservations->where('status', '!=', 'cancelled')
                                                ->filter(function($reservation) use ($currentDateString) {
                                                    return $reservation->end_datetime->format('Y-m-d') === $currentDateString;
                                                })
                                                ->count();
                                        });
                                        
                                        // その日の在庫数を計算
                                        $totalCars = $cars->count();
                                        $reservedCars = $cars->sum(function($car) use ($currentDateString) {
                                            return $car->reservations->where('status', '!=', 'cancelled')
                                                ->filter(function($reservation) use ($currentDateString) {
                                                    return $reservation->start_datetime->format('Y-m-d') <= $currentDateString 
                                                        && $reservation->end_datetime->format('Y-m-d') >= $currentDateString;
                                                })
                                                ->count();
                                        });
                                        $availableCars = $totalCars - $reservedCars;
                                    @endphp
                                    <th class="date-cell border-b border-gray-200 px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider {{ $availableCars == 0 ? 'bg-red-50' : '' }}">
                                        <div class="text-center">
                                            <div class="font-bold text-gray-700">{{ $currentDate->format('n/j') }}</div>
                                            <div class="text-xs text-gray-400 font-medium">{{ $currentDate->format('D') }}</div>
                                            
                                            <!-- 在庫数表示 -->
                                            <div class="mt-1 text-xs">
                                                <div class="text-gray-600 font-medium">
                                                    在庫: <span class="{{ $availableCars > 0 ? 'text-green-600' : 'text-red-600' }} font-bold">{{ $availableCars }}</span>
                                                </div>
                                            </div>
                                            
                                            <!-- 出発・返却件数表示 -->
                                            @if($departureCount > 0 || $returnCount > 0)
                                                <div class="mt-1 flex items-center justify-center gap-1 text-xs">
                                                    @if($departureCount > 0)
                                                        <span class="text-green-600 font-medium">🚗{{ $departureCount }}</span>
                                                    @endif
                                                    @if($returnCount > 0)
                                                        <span class="text-blue-600 font-medium">🏁{{ $returnCount }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </th>
                                    @php
                                        $currentDate->addDay();
                                    @endphp
                                @endwhile
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($cars as $car)
                                <tr class="hover:bg-gray-50">
                                    <!-- 車両情報列（固定） -->
                                    <td class="sticky left-0 z-10 bg-white border-r border-gray-200 px-4 py-3 sticky-car-info min-w-64">
                                        <div class="bg-blue-50 rounded-lg p-3 border border-blue-200 shadow-sm">
                                            <div class="flex items-start justify-between mb-2">
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center mb-2">
                                                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                                                        <h4 class="text-sm font-semibold text-gray-900 truncate">
                                                            {{ $car->carModel->name }}
                                                        </h4>
                                                    </div>
                                                    <div class="space-y-1 mb-2">
                                                        <!-- 状態表示行 -->
                                                        <div class="flex items-center space-x-2">
                                                            <span class="text-xs text-gray-600 font-medium">状態:</span>
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $car->is_public ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                                <span class="w-1.5 h-1.5 rounded-full mr-1 {{ $car->is_public ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                                                {{ $car->is_public ? '公開中' : '非公開中' }}
                                                            </span>
                                                        </div>
                                                        
                                                        <!-- 編集ボタン行 -->
                                                        <div class="flex items-center space-x-2">
                                                            <span class="text-xs text-gray-600 font-medium">編集:</span>
                                                            <button class="toggle-visibility-btn text-xs px-2 py-1 rounded border hover:bg-gray-50 transition-colors {{ $car->is_public ? 'text-red-600 border-red-200 hover:bg-red-50' : 'text-green-600 border-green-200 hover:bg-green-50' }}"
                                                                    data-car-id="{{ $car->id }}"
                                                                    data-current-status="{{ $car->is_public ? 'public' : 'private' }}">
                                                                {{ $car->is_public ? '非公開にする' : '公開にする' }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <button class="add-reservation-btn reservation-button w-full bg-blue-600 text-white px-4 py-4 rounded-lg text-base font-medium hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105"
                                                        data-car-id="{{ $car->id }}"
                                                        data-car-name="{{ $car->carModel->name }}"
                                                        data-car-price="{{ $car->price }}">
                                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                    予約追加
                                                </button>

                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- 日付列 -->
                                    @php
                                        // 今日を基準として前後1ヶ月を表示
                                        $baseDate = Carbon\Carbon::today(); // 今日
                                        $currentDate = $baseDate->copy()->subMonth(); // 1ヶ月前から開始
                                        $endDate = $baseDate->copy()->addMonth(); // 1ヶ月後まで表示
                                    @endphp
                                    @while($currentDate <= $endDate)
                                        @php
                                            $dayReservations = $car->reservations->filter(function($reservation) use ($currentDate) {
                                                return $reservation->status != 'cancelled' &&
                                                       $reservation->start_datetime->format('Y-m-d') <= $currentDate->format('Y-m-d') &&
                                                       $reservation->end_datetime->format('Y-m-d') >= $currentDate->format('Y-m-d');
                                            });
                                            
                                            $isToday = $currentDate->isToday();
                                            $isWeekend = $currentDate->isWeekend();
                                            $isPast = $currentDate->isPast() && !$isToday;
                                            
                                            // テスト用：最初の車両を8/15車検に設定（実際のデータベース対応版は下のコメントアウト）
                                            $isInspectionDay = false;
                                            if ($car->id === $cars->first()->id && $currentDate->format('m-d') === '08-15') {
                                                $isInspectionDay = true;
                                            }
                                            // 実際のデータベース対応版：
                                            // $isInspectionDay = $car->inspection_date && $car->inspection_date->format('Y-m-d') === $currentDate->format('Y-m-d');
                                            
                                            // 予約期間の開始・終了・中間を判定（修正版）
                                            $isReservationStart = $car->reservations->contains(function($reservation) use ($currentDate) {
                                                return $reservation->status != 'cancelled' && 
                                                       $reservation->start_datetime->format('Y-m-d') === $currentDate->format('Y-m-d');
                                            });
                                            
                                            $isReservationEnd = $car->reservations->contains(function($reservation) use ($currentDate) {
                                                return $reservation->status != 'cancelled' && 
                                                       $reservation->end_datetime->format('Y-m-d') === $currentDate->format('Y-m-d');
                                            });
                                            
                                            $isReservationMiddle = $car->reservations->contains(function($reservation) use ($currentDate) {
                                                return $reservation->status != 'cancelled' && 
                                                       $reservation->start_datetime->format('Y-m-d') < $currentDate->format('Y-m-d') &&
                                                       $reservation->end_datetime->format('Y-m-d') > $currentDate->format('Y-m-d');
                                            });
                                            
                                            // 予約期間のクラスを決定
                                            $reservationClass = '';
                                            if ($isReservationStart) {
                                                $reservationClass = 'reservation-start';
                                            } elseif ($isReservationEnd) {
                                                $reservationClass = 'reservation-end';
                                            } elseif ($isReservationMiddle) {
                                                $reservationClass = 'reservation-middle';
                                            }
                                            
                                            // 保留予約の場合は黄色系
                                            if ($dayReservations->where('status', 'pending')->count() > 0) {
                                                if ($isReservationStart) {
                                                    $reservationClass = 'reservation-pending-start';
                                                } elseif ($isReservationEnd) {
                                                    $reservationClass = 'reservation-pending-end';
                                                } elseif ($isReservationMiddle) {
                                                    $reservationClass = 'reservation-pending-middle';
                                                }
                                            }
                                            
                                            $currentDateFormatted = $currentDate->format('Y-m-d');
                                        @endphp
                                        
                                        <td class="date-cell border border-gray-200 px-1 py-2 text-center relative
                                                    {{ $isToday ? 'today-cell' : '' }}
                                                    {{ $isWeekend ? 'weekend-cell' : '' }}
                                                    {{ $isPast ? 'past-cell' : '' }}
                                                    {{ $isInspectionDay ? 'inspection-cell' : '' }}
                                                    {{ $reservationClass }}"
                                            data-date="{{ $currentDateFormatted }}"
                                            data-car-id="{{ $car->id }}"
                                            data-car-name="{{ $car->carModel->name }}"
                                            data-car-price="{{ $car->price }}"
                                            {{ $isPast ? 'data-past="true"' : '' }}>
                                            
                                            @if($dayReservations->count() > 0)
                                                @foreach($dayReservations as $reservation)
                                                    <div class="reservation-block mb-1 p-2 rounded-lg text-xs cursor-pointer font-medium shadow-sm hover:shadow-md transition-all duration-200
                                                                {{ $reservation->status === 'confirmed' ? 'bg-green-100 text-green-800 border-2 border-green-300 hover:bg-green-200' : '' }}
                                                                {{ $reservation->status === 'pending' ? 'bg-yellow-100 text-yellow-800 border-2 border-yellow-300 hover:bg-yellow-200' : '' }}
                                                                {{ $reservation->status === 'cancelled' ? 'bg-red-100 text-red-800 border-2 border-red-300 hover:bg-red-200' : '' }}"
                                                         data-reservation-id="{{ $reservation->id }}"
                                                         data-reservation-start="{{ $reservation->start_datetime->format('Y-m-d') }}"
                                                         data-reservation-end="{{ $reservation->end_datetime->format('Y-m-d') }}"
                                                         title="{{ $reservation->name_kanji ?? $reservation->user->name ?? 'ゲスト' }} - {{ $reservation->status === 'confirmed' ? '確定' : ($reservation->status === 'pending' ? '保留' : 'キャンセル') }} ({{ $reservation->start_datetime->format('m/d H:i') }}〜{{ $reservation->end_datetime->format('m/d H:i') }})">
                                                        @if($isReservationStart)
                                                            <div class="font-bold text-sm">{{ Str::limit($reservation->name_kanji ?? $reservation->user->name ?? 'ゲスト', 8) }}</div>
                                                            <div class="text-xs opacity-80 mt-1 font-medium">{{ $reservation->start_datetime->format('m/d H:i') }}〜{{ $reservation->end_datetime->format('m/d H:i') }}</div>
                                                        @else
                                                            <div class="text-center opacity-70 font-bold">●</div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="text-gray-300 text-xs">-</div>
                                            @endif
                                            
                                            <!-- 車検マーク -->
                                            @if($isInspectionDay)
                                                <div class="absolute inset-0 z-20 flex items-center justify-center pointer-events-none">
                                                    <div class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded shadow-lg border-2 border-red-600">
                                                        🔧 車検最終日
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <!-- 予約追加ボタン（ホバー時表示、予約がない日かつ過去でない場合のみ） -->
                                            @if($dayReservations->count() == 0 && !$isPast)
                                                <button class="add-reservation-day-btn reservation-button absolute inset-0 w-full h-full opacity-0 hover:opacity-100 transition-opacity bg-blue-100 text-blue-600 text-xs font-medium rounded"
                                                        data-car-id="{{ $car->id }}"
                                                        data-car-name="{{ $car->carModel->name }}"
                                                        data-car-price="{{ $car->price }}"
                                                        data-date="{{ $currentDateFormatted }}">
                                                    +
                                                </button>
                                            @endif
                                        </td>
                                        @php
                                            $currentDate = $currentDate->copy()->addDay();
                                        @endphp
                                    @endwhile
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 予約作成モーダル -->
    <div id="reservation-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-6 border w-4/5 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-6">新規予約作成</h3>
                <form id="reservation-form" method="POST" action="{{ route('admin.reservations.store') }}">
                    @csrf
                    <input type="hidden" name="car_id" id="modal-car-id">
                    <input type="hidden" name="start_datetime" id="modal-start-datetime">
                    <input type="hidden" name="end_datetime" id="modal-end-datetime">
                    
                    <!-- 営業時間情報 -->
                    @if($parsedBusinessHours)
                        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <strong>営業時間:</strong> {{ $parsedBusinessHours['start'] }}〜{{ $parsedBusinessHours['end'] }}
                            </p>
                            <p class="text-xs text-blue-600 mt-1">
                                ※ 営業時間外の時間は選択できません
                            </p>
                        </div>
                    @endif
                    
                    <div class="grid grid-cols-2 gap-6">
                        <!-- 左列 -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">選択車両</label>
                                <p class="mt-1 text-sm text-gray-900" id="modal-car-name"></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">開始日<span class="text-red-500">*</span></label>
                                <input type="text" name="start_date" id="modal-start-date" required 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">開始時間<span class="text-red-500">*</span></label>
                                <select name="start_time" id="modal-start-time" required 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @php
                                        $businessStartHour = $parsedBusinessHours ? (int)substr($parsedBusinessHours['start'], 0, 2) : 8;
                                        $businessStartMinute = $parsedBusinessHours ? (int)substr($parsedBusinessHours['start'], 3, 2) : 0;
                                        $businessEndHour = $parsedBusinessHours ? (int)substr($parsedBusinessHours['end'], 0, 2) : 21;
                                        $businessEndMinute = $parsedBusinessHours ? (int)substr($parsedBusinessHours['end'], 3, 2) : 0;
                                    @endphp
                                    @for ($hour = $businessStartHour; $hour <= $businessEndHour; $hour++)
                                        @for ($minute = 0; $minute < 60; $minute += 5)
                                            @php
                                                $time = sprintf('%02d:%02d', $hour, $minute);
                                                $selected = '08:00' == $time ? 'selected' : '';
                                                
                                                // 営業時間の範囲内かどうかを厳密にチェック
                                                $timeMinutes = $hour * 60 + $minute;
                                                $startMinutes = $businessStartHour * 60 + $businessStartMinute;
                                                $endMinutes = $businessEndHour * 60 + $businessEndMinute;
                                                $isWithinBusinessHours = $timeMinutes >= $startMinutes && $timeMinutes <= $endMinutes;
                                                
                                                // 営業時間外の場合はスキップ
                                                if (!$isWithinBusinessHours) {
                                                    continue;
                                                }
                                            @endphp
                                            <option value="{{ $time }}" {{ $selected }}>{{ $time }}</option>
                                        @endfor
                                    @endfor
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">終了日<span class="text-red-500">*</span></label>
                                <input type="text" name="end_date" id="modal-end-date" required 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">終了時間<span class="text-red-500">*</span></label>
                                <select name="end_time" id="modal-end-time" required 
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @for ($hour = $businessStartHour; $hour <= $businessEndHour; $hour++)
                                        @for ($minute = 0; $minute < 60; $minute += 5)
                                            @php
                                                $time = sprintf('%02d:%02d', $hour, $minute);
                                                $selected = '21:00' == $time ? 'selected' : '';
                                                
                                                // 営業時間の範囲内かどうかを厳密にチェック
                                                $timeMinutes = $hour * 60 + $minute;
                                                $startMinutes = $businessStartHour * 60 + $businessStartMinute;
                                                $endMinutes = $businessEndHour * 60 + $businessEndMinute;
                                                $isWithinBusinessHours = $timeMinutes >= $startMinutes && $timeMinutes <= $endMinutes;
                                                
                                                // 営業時間外の場合はスキップ
                                                if (!$isWithinBusinessHours) {
                                                    continue;
                                                }
                                            @endphp
                                            <option value="{{ $time }}" {{ $selected }}>{{ $time }}</option>
                                        @endfor
                                    @endfor
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">利用期間</label>
                                <p class="mt-1 text-sm text-gray-900" id="modal-period"></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">料金</label>
                                <p class="mt-1 text-lg font-semibold text-blue-600" id="modal-price"></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">ステータス</label>
                                <select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="pending">保留</option>
                                    <option value="confirmed">確定</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- 右列 -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">顧客名（カタカナ）<span class="text-red-500">*</span></label>
                                <input type="text" name="name_kanji" required 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="例: ヤマダ タロウ">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">メールアドレス</label>
                                <input type="email" name="email" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="例: example@email.com">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">電話番号<span class="text-red-500">*</span></label>
                                <input type="text" name="phone_main" required 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">備考</label>
                                <textarea name="notes" rows="4" 
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                        <button type="button" id="cancel-btn" 
                                class="bg-gray-300 text-gray-700 px-6 py-2 rounded hover:bg-gray-400">
                            キャンセル
                        </button>
                        <button type="submit" 
                                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                            予約作成
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <style>
        /* 予約期間の視覚化 - 統一された色 */
        .reservation-start,
        .reservation-end,
        .reservation-middle {
            background-color: #e0f2fe !important;
            position: relative;
        }
        
        .reservation-start {
            border-left: 3px solid #0284c7 !important;
        }
        
        .reservation-end {
            border-right: 3px solid #0284c7 !important;
        }
        
        .reservation-pending-start,
        .reservation-pending-end,
        .reservation-pending-middle {
            background-color: #fef3c7 !important;
            position: relative;
        }
        
        .reservation-pending-start {
            border-left: 3px solid #d97706 !important;
        }
        
        .reservation-pending-end {
            border-right: 3px solid #d97706 !important;
        }
        
        /* 予約ブロックのスタイル */
        .reservation-block {
            position: relative;
            z-index: 10;
            transition: all 0.2s ease;
            margin: 0;
            border-radius: 6px;
        }
        
        .reservation-block:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        /* 予約期間を繋げて表示するためのスタイル */
        .reservation-start .reservation-block {
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }
        
        .reservation-end .reservation-block {
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
        }
        
        .reservation-middle .reservation-block {
            border-radius: 0;
        }
        
        /* 予約期間の境界線を調整 */
        .reservation-start {
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }
        
        .reservation-end {
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
        }
        
        .reservation-middle {
            border-radius: 0;
        }
        
        /* 今日の日付のハイライト */
        .today-cell {
            background-color: #dbeafe !important;
            border: 2px solid #3b82f6 !important;
        }
        
        /* 週末の背景 - 予約期間より優先度を下げる */
        .weekend-cell:not(.reservation-start):not(.reservation-end):not(.reservation-middle):not(.reservation-pending-start):not(.reservation-pending-end):not(.reservation-pending-middle) {
            background-color: #f9fafb !important;
        }
        
        /* 予約追加ボタンのスタイル */
        .add-reservation-day-btn {
            z-index: 5;
        }
        
        /* 車両情報列の固定 */
        .sticky-car-info {
            position: sticky;
            left: 0;
            z-index: 20;
            background-color: white;
            border-right: 2px solid #e5e7eb;
        }
        
        /* テーブルヘッダーの固定 */
        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 30;
            background-color: #f9fafb;
        }
        
        /* ドラッグ選択のスタイル */
        .date-cell {
            cursor: pointer;
            user-select: none;
        }
        
        .date-cell:hover {
            background-color: #EFF6FF !important;
        }
        
        .date-cell.dragging {
            background-color: #DBEAFE !important;
            border: 2px solid #3B82F6 !important;
        }
        
        .date-cell.selected {
            background-color: #BFDBFE !important;
            border: 2px solid #2563EB !important;
        }
        
        .date-cell.in-range {
            background-color: #DBEAFE !important;
            border: 1px solid #60A5FA !important;
        }
        
        /* ドラッグ中のカーソル */
        .dragging-cursor {
            cursor: grabbing !important;
        }
        
        /* 凡例用のスタイル */
        .legend-confirmed {
            background-color: #e0f2fe !important;
        }
        
        .legend-pending {
            background-color: #fef3c7 !important;
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 今日の日付を基準に初期化
            let currentMonth = new Date();
            console.log('初期化時のcurrentMonth:', currentMonth);
            console.log('今日の日付:', new Date().toISOString());
            let selectedCarId = null;
            let selectedCarName = null;
            let selectedCarPrice = null;
            
            // ドラッグ選択の変数
            let isDragging = false;
            let startDate = null;
            let endDate = null;
            let startCell = null;
            
            // ページ読み込み時に今日の日付にスクロール
            setTimeout(() => {
                const todayCell = document.querySelector('.today-cell');
                if (todayCell) {
                    todayCell.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'nearest', 
                        inline: 'center' 
                    });
                }
            }, 100);
            
            // 月表示の更新
            function updateMonthDisplay() {
                const monthDisplay = document.getElementById('month-display');
                monthDisplay.textContent = currentMonth.getFullYear() + '年' + 
                    String(currentMonth.getMonth() + 1).padStart(2, '0') + '月';
            }
            
            // カレンダーグリッドの更新
            function updateCalendarGrid() {
                console.log('updateCalendarGrid実行');
                console.log('年:', currentMonth.getFullYear());
                console.log('月:', currentMonth.getMonth() + 1);
                
                // ページをリロードして新しい月のデータを取得
                const params = new URLSearchParams(window.location.search);
                params.set('year', currentMonth.getFullYear());
                params.set('month', currentMonth.getMonth() + 1);
                console.log('新しいURL:', window.location.pathname + '?' + params.toString());
                window.location.search = params.toString();
            }
            
            // ドラッグ選択の初期化
            function initializeDragSelection() {
                const dateCells = document.querySelectorAll('td[data-date]');
                
                dateCells.forEach(cell => {
                    // 予約があるセルまたは過去の日付のセルは除外
                    if (cell.querySelector('.reservation-block') || cell.classList.contains('past-cell')) {
                        return;
                    }
                    
                    cell.classList.add('date-cell');
                    
                    // マウスダウンイベント
                    cell.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                        
                        // 予約があるセルまたは過去の日付のセルは除外
                        if (this.querySelector('.reservation-block') || this.classList.contains('past-cell')) {
                            return;
                        }
                        
                        isDragging = true;
                        startDate = this.dataset.date;
                        startCell = this;
                        
                        // 開始セルをハイライト
                        this.classList.add('dragging');
                        document.body.classList.add('dragging-cursor');
                        
                        // 選択範囲をクリア
                        clearSelection();
                    });
                    
                    // マウスオーバーイベント
                    cell.addEventListener('mouseenter', function(e) {
                        if (!isDragging || !startCell) return;
                        
                        // 予約があるセルまたは過去の日付のセルは除外
                        if (this.querySelector('.reservation-block') || this.classList.contains('past-cell')) {
                            return;
                        }
                        
                        // 同じ車両のセルかチェック
                        if (this.dataset.carId !== startCell.dataset.carId) {
                            return;
                        }
                        
                        endDate = this.dataset.date;
                        updateSelection(startDate, endDate, startCell.dataset.carId);
                    });
                });
                
                // マウスアップイベント（ドキュメント全体）
                document.addEventListener('mouseup', function(e) {
                    if (isDragging && startDate && endDate) {
                        // ドラッグ終了時の処理
                        isDragging = false;
                        document.body.classList.remove('dragging-cursor');
                        
                        // 選択された車両情報を取得
                        const carId = startCell.dataset.carId;
                        const carName = startCell.dataset.carName;
                        const carPrice = parseInt(startCell.dataset.carPrice);
                        
                        // モーダルを表示
                        selectedCarId = carId;
                        selectedCarName = carName;
                        selectedCarPrice = carPrice;
                        
                        // 日付を設定（タイムゾーン問題を回避）
                        const startDateObj = new Date(startDate + 'T00:00:00');
                        const endDateObj = new Date(endDate + 'T00:00:00');
                        
                        // ローカルタイムゾーンでの日付を取得
                        const startDateLocal = startDateObj.toLocaleDateString('en-CA'); // YYYY-MM-DD形式
                        const endDateLocal = endDateObj.toLocaleDateString('en-CA'); // YYYY-MM-DD形式
                        
                        document.getElementById('modal-start-date').value = startDateLocal;
                        document.getElementById('modal-end-date').value = endDateLocal;
                        
                        showReservationModal();
                        
                        // 選択をクリア
                        clearSelection();
                    }
                    
                    isDragging = false;
                    startDate = null;
                    endDate = null;
                    startCell = null;
                });
            }
            
            // 選択範囲の更新
            function updateSelection(start, end, carId) {
                clearSelection();
                
                const startDate = new Date(start);
                const endDate = new Date(end);
                
                // 日付の順序を正規化
                if (startDate > endDate) {
                    [startDate, endDate] = [endDate, startDate];
                }
                
                const dateCells = document.querySelectorAll('td[data-date]');
                
                dateCells.forEach(cell => {
                    // 同じ車両のセルかチェック
                    if (cell.dataset.carId !== carId) {
                        return;
                    }
                    
                    const cellDate = new Date(cell.dataset.date);
                    
                    if (cellDate >= startDate && cellDate <= endDate) {
                        // 予約があるセルは除外
                        if (cell.querySelector('.reservation-block')) {
                            return;
                        }
                        
                        if (cellDate.getTime() === startDate.getTime()) {
                            cell.classList.add('selected');
                        } else if (cellDate.getTime() === endDate.getTime()) {
                            cell.classList.add('selected');
                        } else {
                            cell.classList.add('in-range');
                        }
                    }
                });
            }
            
            // 選択のクリア
            function clearSelection() {
                const dateCells = document.querySelectorAll('td[data-date]');
                dateCells.forEach(cell => {
                    cell.classList.remove('dragging', 'selected', 'in-range');
                });
            }
            
            // 予約追加ボタンのイベント
            document.querySelectorAll('.add-reservation-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    selectedCarId = this.dataset.carId;
                    selectedCarName = this.dataset.carName;
                    selectedCarPrice = parseInt(this.dataset.carPrice);
                    
                    // 今日の日付と営業時間内のデフォルト時間を設定
                    const today = new Date();
                    document.getElementById('modal-start-date').value = today.toISOString().split('T')[0];
                    document.getElementById('modal-end-date').value = today.toISOString().split('T')[0];
                    
                    // 営業時間内のデフォルト値を設定
                    const businessStartTime = '{{ $parsedBusinessHours["start"] ?? "08:00" }}';
                    const businessEndTime = '{{ $parsedBusinessHours["end"] ?? "21:00" }}';
                    document.getElementById('modal-start-time').value = businessStartTime;
                    document.getElementById('modal-end-time').value = businessEndTime;
                    
                    showReservationModal();
                });
            });
            

            // 日付セルの予約追加ボタンのイベント
            document.querySelectorAll('.add-reservation-day-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    
                    selectedCarId = this.dataset.carId;
                    selectedCarName = this.dataset.carName;
                    selectedCarPrice = parseInt(this.dataset.carPrice);
                    
                    // 選択された日付と営業時間内のデフォルト時間を設定（タイムゾーン問題を回避）
                    const selectedDate = this.dataset.date;
                    const selectedDateObj = new Date(selectedDate + 'T00:00:00');
                    const selectedDateLocal = selectedDateObj.toLocaleDateString('en-CA'); // YYYY-MM-DD形式
                    
                    document.getElementById('modal-start-date').value = selectedDateLocal;
                    document.getElementById('modal-end-date').value = selectedDateLocal;
                    
                    // 営業時間内のデフォルト値を設定
                    const businessStartTime = '{{ $parsedBusinessHours["start"] ?? "08:00" }}';
                    const businessEndTime = '{{ $parsedBusinessHours["end"] ?? "21:00" }}';
                    document.getElementById('modal-start-time').value = businessStartTime;
                    document.getElementById('modal-end-time').value = businessEndTime;
                    
                    showReservationModal();
                });
            });
            
            // 予約ブロックのクリックイベント
            document.querySelectorAll('.reservation-block').forEach(function(item) {
                item.addEventListener('click', function() {
                    const reservationId = this.dataset.reservationId;
                    window.open('{{ route("admin.reservations.show", ["reservation" => ":id"]) }}'.replace(':id', reservationId), '_blank');
                });
            });
            
            // 予約作成モーダル表示
            function showReservationModal() {
                document.getElementById('modal-car-id').value = selectedCarId;
                document.getElementById('modal-car-name').textContent = selectedCarName;
                
                updatePriceCalculation();
                document.getElementById('reservation-modal').classList.remove('hidden');
            }
            
            // 料金計算
            function updatePriceCalculation() {
                const startDate = new Date(document.getElementById('modal-start-date').value);
                const endDate = new Date(document.getElementById('modal-end-date').value);
                
                if (startDate && endDate && startDate <= endDate) {
                    const diffTime = Math.abs(endDate - startDate);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    const totalPrice = diffDays * selectedCarPrice;
                    
                    document.getElementById('modal-period').textContent = diffDays + '日間';
                    document.getElementById('modal-price').textContent = '¥' + totalPrice.toLocaleString();
                    
                    // 隠しフィールドに値を設定
                    if (!document.getElementById('start_datetime')) {
                        const startInput = document.createElement('input');
                        startInput.type = 'hidden';
                        startInput.name = 'start_datetime';
                        startInput.id = 'start_datetime';
                        document.getElementById('reservation-form').appendChild(startInput);
                    }
                    
                    if (!document.getElementById('end_datetime')) {
                        const endInput = document.createElement('input');
                        endInput.type = 'hidden';
                        endInput.name = 'end_datetime';
                        endInput.id = 'end_datetime';
                        document.getElementById('reservation-form').appendChild(endInput);
                    }
                    
                    if (!document.getElementById('total_price')) {
                        const totalPriceInput = document.createElement('input');
                        totalPriceInput.type = 'hidden';
                        totalPriceInput.name = 'total_price';
                        totalPriceInput.id = 'total_price';
                        document.getElementById('reservation-form').appendChild(totalPriceInput);
                    }
                    
                    // 必須フィールドを追加
                    const requiredFields = ['name_kana_sei', 'name_kana_mei', 'number_of_adults', 'number_of_children'];
                    requiredFields.forEach(field => {
                        if (!document.getElementById(field)) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = field;
                            input.id = field;
                            input.value = field === 'number_of_adults' ? '1' : '0';
                            document.getElementById('reservation-form').appendChild(input);
                        }
                    });
                    
                    // 日付と時間を組み合わせてISO形式に変換
                    const startTime = document.getElementById('modal-start-time').value;
                    const endTime = document.getElementById('modal-end-time').value;
                    const startDateTime = startDate.toISOString().split('T')[0] + ' ' + startTime;
                    const endDateTime = endDate.toISOString().split('T')[0] + ' ' + endTime;
                    
                    document.getElementById('start_datetime').value = startDateTime;
                    document.getElementById('end_datetime').value = endDateTime;
                    document.getElementById('total_price').value = totalPrice;
                }
            }
            
            // 日付・時間変更時の料金再計算
            document.getElementById('modal-start-date').addEventListener('change', updatePriceCalculation);
            document.getElementById('modal-end-date').addEventListener('change', updatePriceCalculation);
            document.getElementById('modal-start-time').addEventListener('change', updatePriceCalculation);
            document.getElementById('modal-end-time').addEventListener('change', updatePriceCalculation);
            
            // 時間フィールドの営業時間制限
            document.addEventListener('DOMContentLoaded', function() {
                const startTimeInput = document.getElementById('modal-start-time');
                const endTimeInput = document.getElementById('modal-end-time');
                
                if (startTimeInput) {
                    startTimeInput.addEventListener('input', function() {
                        setupTimeRestrictions();
                    });
                    
                    // 営業時間外の入力を防ぐ
                    startTimeInput.addEventListener('change', function() {
                        const value = this.value;
                        const min = this.min;
                        const max = this.max;
                        
                        if (value < min || value > max) {
                            alert('営業時間外の時間は選択できません。営業時間: ' + min + ' - ' + max);
                            this.value = min;
                        }
                    });
                }
                
                if (endTimeInput) {
                    endTimeInput.addEventListener('input', function() {
                        setupTimeRestrictions();
                    });
                    
                    // 営業時間外の入力を防ぐ
                    endTimeInput.addEventListener('change', function() {
                        const value = this.value;
                        const min = this.min;
                        const max = this.max;
                        
                        if (value < min || value > max) {
                            alert('営業時間外の時間は選択できません。営業時間: ' + min + ' - ' + max);
                            this.value = max;
                        }
                    });
                }
            });
            
            // モーダルを閉じる
            document.getElementById('cancel-btn').addEventListener('click', function() {
                document.getElementById('reservation-modal').classList.add('hidden');
            });
            
            // モーダル外クリックで閉じる
            document.getElementById('reservation-modal').addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
            
            // ナビゲーションボタン
            document.getElementById('today-btn').addEventListener('click', function() {
                // 今日の日付に移動
                currentMonth = new Date();
                updateMonthDisplay();
                updateCalendarGrid();
            });
            
            document.getElementById('prev-btn').addEventListener('click', function() {
                console.log('前月ボタンクリック');
                console.log('現在の月:', currentMonth);
                currentMonth.setMonth(currentMonth.getMonth() - 1);
                console.log('変更後の月:', currentMonth);
                updateMonthDisplay();
                updateCalendarGrid();
            });
            
            document.getElementById('next-btn').addEventListener('click', function() {
                console.log('翌月ボタンクリック');
                console.log('現在の月:', currentMonth);
                currentMonth.setMonth(currentMonth.getMonth() + 1);
                console.log('変更後の月:', currentMonth);
                updateMonthDisplay();
                updateCalendarGrid();
            });
            
            // フォーム送信後の処理
            document.getElementById('reservation-form').addEventListener('submit', function(e) {
                console.log('フォーム送信開始');
                
                const nameKanji = document.querySelector('input[name="name_kanji"]').value;
                const email = document.querySelector('input[name="email"]').value;
                const phoneMain = document.querySelector('input[name="phone_main"]').value;
                
                console.log('入力値:', { nameKanji, email, phoneMain });
                
                if (!nameKanji || !phoneMain) {
                    e.preventDefault();
                    alert('必須項目を入力してください。');
                    return false;
                }
                
                // 隠しフィールドの値を確実に設定
                const startDate = new Date(document.getElementById('modal-start-date').value);
                const endDate = new Date(document.getElementById('modal-end-date').value);
                
                console.log('日付:', { startDate, endDate });
                
                if (startDate && endDate && startDate <= endDate) {
                    const diffTime = Math.abs(endDate - startDate);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    const totalPrice = diffDays * selectedCarPrice;
                    
                    console.log('計算結果:', { diffDays, totalPrice, selectedCarPrice });
                    
                    // 隠しフィールドに値を設定
                    if (!document.getElementById('start_datetime')) {
                        const startInput = document.createElement('input');
                        startInput.type = 'hidden';
                        startInput.name = 'start_datetime';
                        startInput.id = 'start_datetime';
                        document.getElementById('reservation-form').appendChild(startInput);
                    }
                    
                    if (!document.getElementById('end_datetime')) {
                        const endInput = document.createElement('input');
                        endInput.type = 'hidden';
                        endInput.name = 'end_datetime';
                        endInput.id = 'end_datetime';
                        document.getElementById('reservation-form').appendChild(endInput);
                    }
                    
                    if (!document.getElementById('total_price')) {
                        const totalPriceInput = document.createElement('input');
                        totalPriceInput.type = 'hidden';
                        totalPriceInput.name = 'total_price';
                        totalPriceInput.id = 'total_price';
                        document.getElementById('reservation-form').appendChild(totalPriceInput);
                    }
                    
                    // 必須フィールドを追加
                    const requiredFields = ['name_kana_sei', 'name_kana_mei', 'number_of_adults', 'number_of_children'];
                    requiredFields.forEach(field => {
                        if (!document.getElementById(field)) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = field;
                            input.id = field;
                            input.value = field === 'number_of_adults' ? '1' : '0';
                            document.getElementById('reservation-form').appendChild(input);
                        }
                    });
                    
                    // 日付と時間を組み合わせてISO形式に変換
                    const startTime = document.getElementById('modal-start-time').value;
                    const endTime = document.getElementById('modal-end-time').value;
                    const startDateTime = startDate.toISOString().split('T')[0] + ' ' + startTime;
                    const endDateTime = endDate.toISOString().split('T')[0] + ' ' + endTime;
                    
                    document.getElementById('start_datetime').value = startDateTime;
                    document.getElementById('end_datetime').value = endDateTime;
                    document.getElementById('total_price').value = totalPrice;
                    
                    // メールアドレスが空の場合はデフォルト値を設定
                    const emailInput = document.querySelector('input[name="email"]');
                    if (emailInput && !emailInput.value.trim()) {
                        emailInput.value = 'guest@example.com';
                    }
                    
                    console.log('フォーム送信準備完了');
                    console.log('送信データ:', {
                        car_id: document.getElementById('modal-car-id').value,
                        start_datetime: document.getElementById('start_datetime').value,
                        end_datetime: document.getElementById('end_datetime').value,
                        total_price: document.getElementById('total_price').value,
                        email: emailInput ? emailInput.value : 'guest@example.com'
                    });
                    
                    // フォーム送信を許可（リロードは削除）
                    return true;
                } else {
                    e.preventDefault();
                    alert('日付が正しく設定されていません。');
                    return false;
                }
            });
            
            // 公開・非公開切り替えボタンのイベント
            document.querySelectorAll('.toggle-visibility-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    
                    const carId = this.dataset.carId;
                    const currentStatus = this.dataset.currentStatus;
                    const newStatus = currentStatus === 'public' ? 'private' : 'public';
                    
                    if (confirm('車両の公開状態を変更しますか？')) {
                        // Ajax request to toggle visibility
                        fetch(`/admin/cars/${carId}/toggle-publish`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                status: newStatus
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // ページをリロードして状態を反映
                                location.reload();
                            } else {
                                alert('エラーが発生しました。');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('エラーが発生しました。');
                        });
                    }
                });
            });

            // ドラッグ選択機能を初期化
            initializeDragSelection();
            
            // カタカナ入力フィールドでIMEモードを設定
            document.addEventListener('DOMContentLoaded', function() {
                const nameInput = document.querySelector('input[name="name_kanji"]');
                if (nameInput) {
                    nameInput.addEventListener('focus', function() {
                        this.style.imeMode = 'active';
                    });
                }
            });
            
            // 営業時間の設定
            const businessHours = @json($businessHours);
            
            // 営業時間から時間オプションを生成
            function generateTimeOptions(startTime, endTime) {
                const options = [];
                const start = new Date('2000-01-01T' + startTime);
                const end = new Date('2000-01-01T' + endTime);
                
                while (start <= end) {
                    const timeString = start.toTimeString().slice(0, 5);
                    options.push(timeString);
                    start.setMinutes(start.getMinutes() + 30); // 30分刻み
                }
                
                return options;
            }
            
            // 時間フィールドを営業時間に制限
            function setupTimeRestrictions() {
                if (!businessHours) {
                    console.log('営業時間が設定されていません');
                    return;
                }
                
                console.log('営業時間:', businessHours);
                
                // 営業時間を解析（8:00-20:00形式）
                const timeMatch = businessHours.match(/(\d{1,2}):(\d{2})[〜\-~]\s*(\d{1,2}):(\d{2})/u);
                if (!timeMatch) {
                    console.log('営業時間の形式が正しくありません:', businessHours);
                    return;
                }
                
                const startHour = parseInt(timeMatch[1]);
                const startMinute = parseInt(timeMatch[2]);
                const endHour = parseInt(timeMatch[3]);
                const endMinute = parseInt(timeMatch[4]);
                
                const startTime = startHour.toString().padStart(2, '0') + ':' + startMinute.toString().padStart(2, '0');
                const endTime = endHour.toString().padStart(2, '0') + ':' + endMinute.toString().padStart(2, '0');
                
                console.log('解析結果:', { startTime, endTime });
                
                // 開始時間フィールドを設定
                const startTimeSelect = document.getElementById('modal-start-time');
                if (startTimeSelect) {
                    // 営業時間外のオプションを削除
                    Array.from(startTimeSelect.options).forEach(option => {
                        const optionTime = option.value;
                        if (optionTime < startTime || optionTime > endTime) {
                            option.remove();
                        }
                    });
                    
                    // 営業時間外の入力を防ぐイベントリスナーを追加
                    startTimeSelect.addEventListener('change', function() {
                        const selectedTime = this.value;
                        if (selectedTime < startTime || selectedTime > endTime) {
                            alert('営業時間外の時間は選択できません。営業時間: ' + startTime + ' - ' + endTime);
                            this.value = startTime; // 営業開始時間にリセット
                        }
                    });
                    
                    console.log('開始時間フィールド設定完了:', startTime);
                }
                
                // 終了時間フィールドを設定
                const endTimeSelect = document.getElementById('modal-end-time');
                if (endTimeSelect) {
                    // 営業時間外のオプションを削除
                    Array.from(endTimeSelect.options).forEach(option => {
                        const optionTime = option.value;
                        if (optionTime < startTime || optionTime > endTime) {
                            option.remove();
                        }
                    });
                    
                    // 営業時間外の入力を防ぐイベントリスナーを追加
                    endTimeSelect.addEventListener('change', function() {
                        const selectedTime = this.value;
                        if (selectedTime < startTime || selectedTime > endTime) {
                            alert('営業時間外の時間は選択できません。営業時間: ' + startTime + ' - ' + endTime);
                            this.value = endTime; // 営業終了時間にリセット
                        }
                    });
                    
                    console.log('終了時間フィールド設定完了:', endTime);
                }
            }
            
            // モーダル表示時にLitepickerと営業時間制限を設定
            const originalShowReservationModal = window.showReservationModal;
            window.showReservationModal = function() {
                originalShowReservationModal();
                // 少し遅延させてからLitepickerと営業時間制限を設定
                setTimeout(function() {
                    setupModalLitepicker();
                    setupModalTimeRestrictions();
                }, 200); // 遅延時間を200msに増加
            };

            // モーダル用Litepickerの初期化
            function setupModalLitepicker() {
                const modalStartDateEl = document.getElementById('modal-start-date');
                const modalEndDateEl = document.getElementById('modal-end-date');
                const modalStartTimeEl = document.getElementById('modal-start-time');
                const modalEndTimeEl = document.getElementById('modal-end-time');

                // 既存のLitepickerインスタンスを破棄
                if (window.modalStartPicker) {
                    window.modalStartPicker.destroy();
                }
                if (window.modalEndPicker) {
                    window.modalEndPicker.destroy();
                }

                // 新しいLitepickerインスタンスを作成
                window.modalStartPicker = new Litepicker({
                    element: modalStartDateEl,
                    format: 'YYYY-MM-DD',
                    minDate: new Date(),
                    setup: (picker) => {
                        picker.on('selected', () => updateModalEndConstraints());
                    }
                });

                window.modalEndPicker = new Litepicker({
                    element: modalEndDateEl,
                    format: 'YYYY-MM-DD',
                    minDate: new Date(),
                    setup: (picker) => {
                        picker.on('selected', () => validateModalEndTime());
                    }
                });

                // イベントリスナーを設定
                [modalStartDateEl, modalStartTimeEl].forEach(el => {
                    el.addEventListener('change', updateModalEndConstraints);
                });

                [modalEndDateEl, modalEndTimeEl].forEach(el => {
                    el.addEventListener('change', validateModalEndTime);
                });
            }

            function updateModalEndConstraints() {
                const modalStartDateEl = document.getElementById('modal-start-date');
                const modalEndDateEl = document.getElementById('modal-end-date');
                const startDate = modalStartDateEl.value;
                if (startDate && window.modalEndPicker) {
                    window.modalEndPicker.setOptions({ minDate: startDate });
                }
                validateModalEndTime();
            }

            function validateModalEndTime() {
                const modalStartDateEl = document.getElementById('modal-start-date');
                const modalStartTimeEl = document.getElementById('modal-start-time');
                const modalEndDateEl = document.getElementById('modal-end-date');
                const modalEndTimeEl = document.getElementById('modal-end-time');

                const startDate = modalStartDateEl.value;
                const startTime = modalStartTimeEl.value;
                const endDate = modalEndDateEl.value;
                const endTime = modalEndTimeEl.value;

                if (!startDate || !startTime || !endDate || !endTime) return;

                const start = new Date(`${startDate}T${startTime}`);
                const end = new Date(`${endDate}T${endTime}`);

                if (end < start) {
                    alert('終了日時は開始日時より後の時間を選択してください。');
                    modalEndTimeEl.value = '';
                    modalEndTimeEl.classList.add('border-red-500');
                    modalEndTimeEl.title = '開始日時より後の時刻を選んでください';
                } else {
                    modalEndTimeEl.classList.remove('border-red-500');
                    modalEndTimeEl.title = '';
                }
            }

            // モーダル用営業時間制限の適用
            function setupModalTimeRestrictions() {
                console.log('setupModalTimeRestrictions関数が呼び出されました');
                
                const modalStartTimeEl = document.getElementById('modal-start-time');
                const modalEndTimeEl = document.getElementById('modal-end-time');
                
                if (!modalStartTimeEl || !modalEndTimeEl) {
                    console.log('モーダルの時間要素が見つかりません');
                    return;
                }
                
                // 営業時間の制限を適用（営業時間外のオプションを削除）
                const businessStartTime = '{{ $parsedBusinessHours["start"] ?? "08:00" }}';
                const businessEndTime = '{{ $parsedBusinessHours["end"] ?? "21:00" }}';
                
                console.log('営業時間制限を適用:', businessStartTime, '-', businessEndTime);
                console.log('開始時間オプション数（削除前）:', modalStartTimeEl.options.length);
                console.log('終了時間オプション数（削除前）:', modalEndTimeEl.options.length);
                
                // 営業時間の分単位を計算
                const businessStartMinutes = parseInt(businessStartTime.split(':')[0]) * 60 + parseInt(businessStartTime.split(':')[1]);
                const businessEndMinutes = parseInt(businessEndTime.split(':')[0]) * 60 + parseInt(businessEndTime.split(':')[1]);
                
                // 利用開始時間の制限（営業時間外のオプションを完全に削除）
                let removedStartOptions = 0;
                for (let i = modalStartTimeEl.options.length - 1; i >= 0; i--) {
                    const option = modalStartTimeEl.options[i];
                    const optionTime = option.value;
                    const optionMinutes = parseInt(optionTime.split(':')[0]) * 60 + parseInt(optionTime.split(':')[1]);
                    
                    // 営業時間外の場合は確実に削除
                    if (optionMinutes < businessStartMinutes || optionMinutes > businessEndMinutes) {
                        modalStartTimeEl.removeChild(option);
                        removedStartOptions++;
                        console.log('開始時間から削除:', optionTime);
                    }
                }
                
                // 利用終了時間の制限（営業時間外のオプションを完全に削除）
                let removedEndOptions = 0;
                for (let i = modalEndTimeEl.options.length - 1; i >= 0; i--) {
                    const option = modalEndTimeEl.options[i];
                    const optionTime = option.value;
                    const optionMinutes = parseInt(optionTime.split(':')[0]) * 60 + parseInt(optionTime.split(':')[1]);
                    
                    // 営業時間外の場合は確実に削除
                    if (optionMinutes < businessStartMinutes || optionMinutes > businessEndMinutes) {
                        modalEndTimeEl.removeChild(option);
                        removedEndOptions++;
                        console.log('終了時間から削除:', optionTime);
                    }
                }
                
                console.log('削除された開始時間オプション:', removedStartOptions);
                console.log('削除された終了時間オプション:', removedEndOptions);
                console.log('残りの開始時間オプション数:', modalStartTimeEl.options.length);
                console.log('残りの終了時間オプション数:', modalEndTimeEl.options.length);
                
                // 営業時間内のオプションのみが残っていることを確認
                if (modalStartTimeEl.options.length > 0) {
                    console.log('開始時間の最初のオプション:', modalStartTimeEl.options[0].value);
                    console.log('開始時間の最後のオプション:', modalStartTimeEl.options[modalStartTimeEl.options.length - 1].value);
                }
                if (modalEndTimeEl.options.length > 0) {
                    console.log('終了時間の最初のオプション:', modalEndTimeEl.options[0].value);
                    console.log('終了時間の最後のオプション:', modalEndTimeEl.options[modalEndTimeEl.options.length - 1].value);
                }
                
                // 営業時間制限が正しく適用されていることを確認
                console.log('営業時間制限の適用完了');
            }
        });
    </script>
    @endpush

    @push('styles')
    <style>
        /* カレンダーテーブルの最適化 */
        #reservation-grid {
            font-size: 0.8rem;
        }
        
        #reservation-grid th,
        #reservation-grid td {
            min-height: 60px;
            vertical-align: top;
            padding: 4px 6px;
        }
        
        /* 日付セルのスタイル */
        .date-cell {
            width: 120px;
            min-width: 120px;
            font-size: 0.75rem;
        }
        
        /* 車両情報セルの最適化 */
        .sticky-car-info {
            width: 200px;
            min-width: 200px;
        }
        
        /* 予約ボタンのサイズ調整 */
        .reservation-button {
            font-size: 0.7rem;
            padding: 2px 6px;
        }
        
        /* 過去の日付のスタイル */
        .past-cell {
            background-color: #f3f4f6;
            color: #9ca3af;
            cursor: not-allowed;
        }
        
        .past-cell:hover {
            background-color: #f3f4f6;
        }
        
        /* 車検日のセルスタイル */
        .inspection-cell {
            background-color: #fef2f2;
            border-color: #fecaca;
        }
        
        .inspection-cell:hover {
            background-color: #fee2e2;
        }
        
        /* レスポンシブ対応 */
        @media (max-width: 1280px) {
            .date-cell {
                width: 100px;
                min-width: 100px;
            }
            .sticky-car-info {
                width: 180px;
                min-width: 180px;
            }
        }
        
        @media (max-width: 1024px) {
            .date-cell {
                width: 80px;
                min-width: 80px;
            }
            .sticky-car-info {
                width: 160px;
                min-width: 160px;
            }
            #reservation-grid {
                font-size: 0.7rem;
            }
        }
    </style>
    @endpush
</x-admin-layout> 