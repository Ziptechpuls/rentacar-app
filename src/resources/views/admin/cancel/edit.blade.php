<x-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('キャンセルポリシーの編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
            <div class="bg-white p-8 rounded shadow">
                <form method="POST" action="{{ route('admin.cancel.update') }}">
                    @csrf
                    @method('PUT')

                    <!-- 戻るボタン -->
                    <div class="flex justify-end mb-6">
                        <a href="{{ route('admin.cancel.index') }}"
                        class="inline-flex items-center px-6 py-3 bg-gray-500 border border-transparent rounded-lg font-semibold text-base text-white uppercase tracking-widest hover:bg-gray-600 transition mr-3">
                            戻る
                        </a>
                        <button type="submit"
                        class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-lg font-semibold text-base text-white uppercase tracking-widest hover:bg-gray-700 transition">
                            更新
                        </button>
                    </div>

                    <!-- エディタ -->
                    <div class="mb-6">
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                            キャンセルポリシーの内容
                        </label>
                        <textarea
                            id="content"
                            name="content"
                            rows="20"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required
                        >{{ old('content', $policy ? $policy->content : '') }}</textarea>
                        @error('content')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
