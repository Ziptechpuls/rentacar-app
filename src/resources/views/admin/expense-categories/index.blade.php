<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('経費項目管理') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            <!-- 新規追加フォーム -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">新規経費項目追加</h3>
                    <form method="POST" action="{{ route('admin.expense-categories.store') }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">項目名</label>
                                <input type="text" name="name" id="name" required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">種別</label>
                                <select name="type" id="type" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="fixed">固定費</option>
                                    <option value="variable">変動費</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" 
                                        class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                    追加
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 経費項目一覧 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">経費項目一覧</h3>
                    
                    @if($categories->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">項目名</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">種別</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">状態</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($categories as $category)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $category->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                    @if($category->type === 'fixed') bg-blue-100 text-blue-800
                                                    @else bg-green-100 text-green-800 @endif">
                                                    {{ $category->type_name }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                    @if($category->is_active) bg-green-100 text-green-800
                                                    @else bg-red-100 text-red-800 @endif">
                                                    {{ $category->is_active ? '有効' : '無効' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex justify-end space-x-2">
                                                    <button onclick="editCategory({{ $category->id }}, '{{ $category->name }}', '{{ $category->type }}')"
                                                            class="text-indigo-600 hover:text-indigo-900">
                                                        編集
                                                    </button>
                                                    <form method="POST" action="{{ route('admin.expense-categories.toggle-active', $category) }}" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" 
                                                                class="text-yellow-600 hover:text-yellow-900">
                                                            {{ $category->is_active ? '無効化' : '有効化' }}
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('admin.expense-categories.destroy', $category) }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                onclick="return confirm('本当に削除しますか？')"
                                                                class="text-red-600 hover:text-red-900">
                                                            削除
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">経費項目がありません。</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- 編集モーダル -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">経費項目編集</h3>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label for="edit_name" class="block text-sm font-medium text-gray-700">項目名</label>
                            <input type="text" name="name" id="edit_name" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label for="edit_type" class="block text-sm font-medium text-gray-700">種別</label>
                            <select name="type" id="edit_type" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="fixed">固定費</option>
                                <option value="variable">変動費</option>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" onclick="closeEditModal()"
                                    class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                                キャンセル
                            </button>
                            <button type="submit"
                                    class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                更新
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editCategory(id, name, type) {
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_type').value = type;
            document.getElementById('editForm').action = `/admin/expense-categories/${id}`;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</x-admin-layout> 