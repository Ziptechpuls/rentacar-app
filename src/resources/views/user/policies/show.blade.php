<x-user-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ $title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
            <div class="bg-white p-8 rounded shadow">
                <!-- ポリシーの内容 -->
                <div class="prose max-w-none">
                    @if($policy)
                        {!! nl2br(e($policy->content)) !!}
                    @else
                        <p class="text-gray-600 text-center py-8">
                            現在準備中です。しばらくお待ちください。
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-user-layout> 