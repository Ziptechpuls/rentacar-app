<x-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('料金情報編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.price.update') }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="base_price" value="基本料金（1日）" />
                            <div class="flex items-center gap-2">
                                <x-text-input id="base_price" name="base_price" type="number" class="block mt-1" value="5000" required />
                                <span class="text-gray-600">円</span>
                            </div>
                            <x-input-error :messages="$errors->get('base_price')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="overtime_price" value="時間超過料金（1時間）" />
                            <div class="flex items-center gap-2">
                                <x-text-input id="overtime_price" name="overtime_price" type="number" class="block mt-1" value="1000" required />
                                <span class="text-gray-600">円</span>
                            </div>
                            <x-input-error :messages="$errors->get('overtime_price')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="insurance_price" value="免責補償料金（1日）" />
                            <div class="flex items-center gap-2">
                                <x-text-input id="insurance_price" name="insurance_price" type="number" class="block mt-1" value="1500" required />
                                <span class="text-gray-600">円</span>
                            </div>
                            <x-input-error :messages="$errors->get('insurance_price')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="night_price" value="深夜料金（22:00-5:00）" />
                            <div class="flex items-center gap-2">
                                <x-text-input id="night_price" name="night_price" type="number" class="block mt-1" value="30" required />
                                <span class="text-gray-600">%増</span>
                            </div>
                            <x-input-error :messages="$errors->get('night_price')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label value="キャンセル料金" />
                            <div class="mt-4 space-y-4">
                                <div>
                                    <x-input-label for="cancel_price_before" value="前日まで" class="text-sm" />
                                    <div class="flex items-center gap-2">
                                        <x-text-input id="cancel_price_before" name="cancel_price_before" type="number" class="block mt-1" value="0" required />
                                        <span class="text-gray-600">%</span>
                                    </div>
                                </div>
                                <div>
                                    <x-input-label for="cancel_price_same_day" value="当日" class="text-sm" />
                                    <div class="flex items-center gap-2">
                                        <x-text-input id="cancel_price_same_day" name="cancel_price_same_day" type="number" class="block mt-1" value="50" required />
                                        <span class="text-gray-600">%</span>
                                    </div>
                                </div>
                                <div>
                                    <x-input-label for="cancel_price_no_show" value="無断キャンセル" class="text-sm" />
                                    <div class="flex items-center gap-2">
                                        <x-text-input id="cancel_price_no_show" name="cancel_price_no_show" type="number" class="block mt-1" value="100" required />
                                        <span class="text-gray-600">%</span>
                                    </div>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('cancel_price_before')" class="mt-2" />
                            <x-input-error :messages="$errors->get('cancel_price_same_day')" class="mt-2" />
                            <x-input-error :messages="$errors->get('cancel_price_no_show')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('更新') }}</x-primary-button>
                            <a href="{{ route('admin.price.index') }}" class="text-gray-600 hover:text-gray-900">キャンセル</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

