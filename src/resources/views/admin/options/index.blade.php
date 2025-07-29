<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                オプション一覧
            </h2>
            <div class="flex gap-2">
                <button id="toggle-sort" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition-colors">
                    並び替えモード
                </button>
                <a href="{{ route('admin.options.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    新規オプション登録
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- 並び替えモードの説明 -->
                    <div id="sort-instructions" class="hidden mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-yellow-800">
                            <strong>並び替えモード：</strong> オプション項目をドラッグ&ドロップして順番を変更できます。変更は自動的に保存されます。
                        </p>
                    </div>

                    <div id="options-list" class="space-y-6">
                        @foreach ($options as $option)
                            <div data-option-id="{{ $option->id }}" class="option-item border rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
                                <div class="flex flex-col md:flex-row gap-4">
                                    <!-- ドラッグハンドル -->
                                    <div class="drag-handle hidden flex-shrink-0 cursor-move flex items-center justify-center w-8 h-8 bg-gray-100 rounded hover:bg-gray-200 transition-colors">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                        </svg>
                                    </div>
                                    <!-- 画像 -->
                                    <div class="w-40 h-40 md:w-56 md:h-56 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden">
                                        @if ($option->image_path)
                                            <img src="{{ asset('storage/' . $option->image_path) }}"
                                                alt="{{ $option->name }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- オプション情報 -->
                                    <div class="flex-grow space-y-4">
                                        <div class="flex flex-col md:flex-row justify-between md:items-start gap-4">
                                            <div class="space-y-2">
                                                <h3 class="text-xl font-bold text-gray-800">{{ $option->name }}</h3>
                                                <p class="text-base text-gray-600 leading-relaxed">{{ $option->description }}</p>
                                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-gray-100 text-gray-800">
                                                        {{ $option->is_quantity ? '数量選択可能' : '数量選択不可' }}
                                                    </span>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800">
                                                        {{ $option->price_type == 'per_piece' ? '1個あたり' : '1日あたり' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-right flex-shrink-0">
                                                <div class="text-2xl font-bold text-gray-800">
                                                    ¥{{ number_format($option->price) }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $option->price_type == 'per_piece' ? '/ 個' : '/ 日' }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 管理用ボタン -->
                                        <div class="flex gap-2 pt-4 border-t">
                                            <a href="{{ route('admin.options.edit', $option) }}"
                                                class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-sm font-medium transition-colors duration-200">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                編集
                                            </a>
                                            <form action="{{ route('admin.options.destroy', $option) }}" method="POST"
                                                onsubmit="return confirm('このオプションを削除してもよろしいですか？');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded text-sm font-medium transition-colors duration-200">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    削除
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if ($options->isEmpty())
                            <div class="text-center py-8 text-gray-600">
                                オプションが登録されていません
                            </div>
                        @endif
                    </div>

                    <!-- ページネーション -->
                    <div class="mt-6">
                        {{ $options->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('toggle-sort');
            const sortInstructions = document.getElementById('sort-instructions');
            const optionsList = document.getElementById('options-list');
            const dragHandles = document.querySelectorAll('.drag-handle');
            const optionItems = document.querySelectorAll('.option-item');
            
            let sortMode = false;
            let sortable = null;
            
            // 並び替えモードの切り替え
            toggleButton.addEventListener('click', function() {
                sortMode = !sortMode;
                
                if (sortMode) {
                    // 並び替えモードON
                    toggleButton.textContent = '並び替え終了';
                    toggleButton.classList.remove('bg-green-600', 'hover:bg-green-700');
                    toggleButton.classList.add('bg-red-600', 'hover:bg-red-700');
                    
                    sortInstructions.classList.remove('hidden');
                    
                    // ドラッグハンドルを表示
                    dragHandles.forEach(handle => {
                        handle.classList.remove('hidden');
                    });
                    
                    // オプション項目をドラッグ可能にする
                    optionItems.forEach(item => {
                        item.classList.add('cursor-move', 'bg-gray-50');
                    });
                    
                    // Sortable.jsを初期化
                    sortable = new Sortable(optionsList, {
                        handle: '.drag-handle',
                        animation: 150,
                        ghostClass: 'opacity-50',
                        chosenClass: 'border-blue-500',
                        dragClass: 'rotate-2',
                        onEnd: function(evt) {
                            // 並び順を取得
                            const order = Array.from(optionsList.children).map(item => 
                                parseInt(item.dataset.optionId)
                            );
                            
                            // サーバーに並び順を送信
                            updateSortOrder(order);
                        }
                    });
                } else {
                    // 並び替えモードOFF
                    toggleButton.textContent = '並び替えモード';
                    toggleButton.classList.remove('bg-red-600', 'hover:bg-red-700');
                    toggleButton.classList.add('bg-green-600', 'hover:bg-green-700');
                    
                    sortInstructions.classList.add('hidden');
                    
                    // ドラッグハンドルを非表示
                    dragHandles.forEach(handle => {
                        handle.classList.add('hidden');
                    });
                    
                    // オプション項目のスタイルを元に戻す
                    optionItems.forEach(item => {
                        item.classList.remove('cursor-move', 'bg-gray-50');
                    });
                    
                    // Sortable.jsを破棄
                    if (sortable) {
                        sortable.destroy();
                        sortable = null;
                    }
                }
            });
            
            // 並び順をサーバーに送信
            function updateSortOrder(order) {
                fetch('{{ route("admin.options.updateSortOrder") }}', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        order: order
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // 成功通知（オプション）
                        showNotification('並び順を更新しました', 'success');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('並び順の更新に失敗しました', 'error');
                });
            }
            
            // 通知表示（簡易版）
            function showNotification(message, type) {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 px-4 py-2 rounded shadow-lg z-50 ${
                    type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
                }`;
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }
        });
    </script>
    @endpush
</x-admin-layout> 