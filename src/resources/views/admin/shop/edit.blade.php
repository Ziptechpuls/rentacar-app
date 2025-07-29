<x-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('店舗情報編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
            <div class="bg-white p-8">
                <form method="POST" action="{{ route('admin.shop.update') }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div>
                            <x-input-label for="name" value="店舗名" />
                            <x-text-input id="name" name="name" type="text" class="block w-full mt-1" :value="old('name', $shop->name ?? '')" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="tel" value="TEL" />
                            <x-text-input id="tel" name="tel" type="tel" class="block w-full mt-1" :value="old('tel', $shop->tel ?? '')" required />
                            <x-input-error :messages="$errors->get('tel')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" value="メールアドレス" />
                            <x-text-input id="email" name="email" type="email" class="block w-full mt-1" :value="old('email', $shop->email ?? '')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="address" value="住所" />
                            <x-text-input id="address" name="address" type="text" class="block w-full mt-1" :value="old('address', $shop->address ?? '')" required />
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="business_hours" value="営業時間" />
                            <x-text-input id="business_hours" name="business_hours" type="text" class="block w-full mt-1" :value="old('business_hours', $shop->business_hours ?? '')" required />
                            <x-input-error :messages="$errors->get('business_hours')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="access" value="アクセス" />
                            <textarea
                                id="access"
                                name="access"
                                rows="3"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            >{{ old('access', $shop->access ?? '') }}</textarea>
                            <x-input-error :messages="$errors->get('access')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="map_iframe" value="Google Maps 埋め込みコード" />
                            <textarea
                                id="map_iframe"
                                name="map_iframe"
                                rows="4"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            >{{ old('map_iframe', $shop->map_iframe ?? '') }}</textarea>
                            <p class="mt-2 text-sm text-gray-500">Google Mapsの埋め込みiframeコードを貼り付けてください。<br>
                            なお、width="100%",height="400"が推奨です。</p>
                            <x-input-error :messages="$errors->get('map_iframe')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex justify-end items-center gap-4 mt-6">
                        <a href="{{ route('admin.shop.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">キャンセル</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('更新') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
