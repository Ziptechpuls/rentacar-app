<x-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('利用規約') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
            <div class="bg-white p-8 rounded shadow">
                <!-- 編集ボタン -->
                <div class="flex justify-end mb-6">
                    <a href="{{ route('admin.terms.edit') }}"
                    class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-lg font-semibold text-base text-white uppercase tracking-widest hover:bg-gray-700 transition">
                        編集
                    </a>
                </div>

                <!-- 利用規約の内容 -->
                <div class="prose max-w-none">
                    @if($policy)
                        {!! nl2br(e($policy->content)) !!}
                    @else
                        <p class="text-gray-600 text-center py-8">
                            利用規約が設定されていません。
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
