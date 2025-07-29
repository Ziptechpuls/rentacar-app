<x-user-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ $car->name }} の詳細
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:space-x-6">
                <!-- 左カラム -->
                <div class="md:w-1/2 space-y-6">
                    <!-- 予約概要サマリー -->
                    <div class="bg-white shadow p-6 rounded">
                        <h3 class="text-lg font-semibold mb-4">予約内容の確認</h3>
                        <ul class="text-sm text-gray-700 space-y-2">
                            <li>利用開始：{{ $start ? $start->format('Y年m月d日 H:i') : '未指定' }}</li>
                            <li>利用終了：{{ $end ? $end->format('Y年m月d日 H:i') : '未指定' }}</li>
                            <li>期間：{{ $isSameDay ? '日帰り' : "{$nights}泊{$days}日" }}</li>
                            <li>1日あたり：¥{{ number_format($car->price) }}</li>
                            <li>合計料金（税込）：¥{{ number_format($totalPrice) }}</li>
                        </ul>
                    </div>

                    <!-- 車両情報 -->
                    <div class="bg-white shadow p-6 rounded">
                        <h3 class="text-lg font-semibold mb-4">車両情報</h3>
                        <div class="flex flex-col sm:flex-row gap-6">
                            <div class="sm:w-1/2">
                                @if ($car->description)
                                    <div class="text-gray-700">
                                        <p>{{ $car->description }}</p>
                                    </div>
                                @endif

                                @if ($car->images && $car->images->count())
                                    <img src="{{ asset('storage/' . $car->images->first()->path) }}"
                                         alt="car_picture"
                                         class="w-full rounded object-cover">
                                @else
                                    <div class="w-full h-40 bg-gray-300 flex items-center justify-center rounded text-gray-600">
                                        画像なし
                                    </div>
                                @endif
                            </div>

                            <div class="sm:w-1/2 space-y-2">
                                <div><strong>車名：</strong>{{ $car->name }}</div>
                                <div><strong>車種：</strong>{{ $car->type }}</div>
                                <div><strong>乗車人数：</strong>{{ $car->capacity }}人</div>
                                <div><strong>ミッション：</strong>{{ $car->transmission }}</div>
                                <div><strong>喫煙可否：</strong>{{ $car->smoking_preference_label }}</div>

                                <div class="flex flex-wrap gap-2 mt-2 text-sm">
                                    @php
                                        $badge = "bg-gray-200 px-2 py-1 rounded";
                                    @endphp
                                    @foreach ($car->equipment_list as $equipment)
                                        <span class="{{ $badge }}">{{ $equipment['icon'] }} {{ $equipment['label'] }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 右カラム -->
                <div class="md:w-1/2 bg-white shadow p-6 rounded mt-6 md:mt-0">
                    <form action="{{ route('user.cars.reservations.show-option-confirm', ['car' => $car->id]) }}" method="GET">
                        <input type="hidden" name="start_datetime" value="{{ old('start_datetime', request('start_datetime')) }}">
                        <input type="hidden" name="end_datetime" value="{{ old('end_datetime', request('end_datetime')) }}">

                        <h3 class="text-lg font-semibold mb-4">オプション選択</h3>

                        <div class="space-y-4 max-h-[1000px] overflow-y-auto">
                            @foreach ($options as $option)
                                <div class="border-b pb-4 pt-4 space-y-4 md:space-y-0 md:flex md:items-start md:gap-12 relative">
                                    <!-- 画像 -->
                                    <div class="w-32 h-32 sm:w-36 sm:h-36 lg:w-40 lg:h-40 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden shadow-sm mx-auto md:mx-0">
                                        @if ($option->image_path)
                                            <img src="{{ asset('storage/' . $option->image_path) }}"
                                                 alt="{{ $option->name }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-8 h-8 sm:w-12 sm:h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- テキスト情報 -->
                                    <div class="flex-1 max-w-xs text-left px-4">
                                        <div class="font-semibold text-gray-800 text-lg md:text-base">{{ $option->name }}</div>
                                        @if ($option->description)
                                            <div class="text-sm text-gray-600 mt-1">{{ $option->description }}</div>
                                        @endif
                                    </div>

                                    <!-- 料金＋入力欄 -->
                                    <div class="md:text-right md:ml-auto md:min-w-[160px]">
                                        <div class="mb-1 text-gray-700">
                                            料金：+¥{{ number_format($option->price) }}
                                            <span class="text-sm text-gray-500">
                                                / {{ $option->price_type === 'per_piece' ? '個' : '日' }}
                                            </span>
                                        </div>
                                        @if ($option->is_quantity)
                                            <label class="block mb-1 text-gray-700 font-medium">数量</label>
                                            <select name="options[{{ $option->id }}]" class="w-32 md:w-full border border-gray-300 rounded px-2 py-1">
                                                @for ($i = 0; $i <= 5; $i++)
                                                    <option value="{{ $i }}"
                                                        @selected(old("options.{$option->id}", request("options.{$option->id}")) == $i)>
                                                        {{ $i }}個
                                                    </option>
                                                @endfor
                                            </select>
                                        @else
                                            <label class="inline-flex items-center cursor-pointer select-none">
                                                <input
                                                    type="checkbox"
                                                    name="options[{{ $option->id }}]"
                                                    value="1"
                                                    class="peer hidden"
                                                    @checked(old("options.{$option->id}", request("options.{$option->id}")) == 1)>
                                                <span class="px-4 py-1 rounded border border-gray-300 peer-checked:bg-blue-600 peer-checked:text-white transition">
                                                    選択する
                                                </span>
                                            </label>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded mt-6">
                            予約内容を確認する
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-user-layout>