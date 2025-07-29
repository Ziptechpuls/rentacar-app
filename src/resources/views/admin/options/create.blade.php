<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            オプション新規登録
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.options.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <!-- 名前 -->
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">オプション名</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                required>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 説明 -->
                        <div>
                            <label for="description" class="block font-medium text-sm text-gray-700">説明</label>
                            <textarea name="description" id="description" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 料金 -->
                        <div class="space-y-4">
                            <div>
                                <label for="price" class="block font-medium text-sm text-gray-700">料金</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500">¥</span>
                                    <input type="number" name="price" id="price" value="{{ old('price') }}"
                                        class="flex-1 block w-full rounded-none rounded-r-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        required min="0">
                                </div>
                                @error('price')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="price_type" class="block font-medium text-sm text-gray-700">料金タイプ</label>
                                <select name="price_type" id="price_type"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="per_piece" {{ old('price_type') == 'per_piece' ? 'selected' : '' }}>1個あたりの料金</option>
                                    <option value="per_day" {{ old('price_type', 'per_day') == 'per_day' ? 'selected' : '' }}>1日あたりの料金</option>
                                </select>
                                @error('price_type')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- 数量選択可否 -->
                        <div>
                            <label for="is_quantity" class="block font-medium text-sm text-gray-700">数量選択</label>
                            <select name="is_quantity" id="is_quantity"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="1" {{ old('is_quantity') == '1' ? 'selected' : '' }}>数量選択可能</option>
                                <option value="0" {{ old('is_quantity') == '0' ? 'selected' : '' }}>数量選択不可（チェックボックス）</option>
                            </select>
                            @error('is_quantity')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 画像 -->
                        <div>
                            <label for="image" class="block font-medium text-sm text-gray-700">画像</label>
                            <input type="file" name="image" id="image" accept="image/*"
                                class="mt-1 block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-blue-50 file:text-blue-700
                                    hover:file:bg-blue-100">
                            @error('image')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('admin.options.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                キャンセル
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                登録する
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout> 