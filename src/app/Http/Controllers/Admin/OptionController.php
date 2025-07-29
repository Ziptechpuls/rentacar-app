<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OptionController extends Controller
{
    public function index()
    {
        $options = Option::paginate(10);
        return view('admin.options.index', compact('options'));
    }

    public function create()
    {
        return view('admin.options.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'is_quantity' => 'required|boolean',
            'price_type' => 'required|in:per_piece,per_day',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('options', 'public');
            $validated['image_path'] = $path;
        }

        Option::create($validated);

        return redirect()
            ->route('admin.options.index')
            ->with('success', 'オプションを登録しました。');
    }

    public function edit(Option $option)
    {
        return view('admin.options.edit', compact('option'));
    }

    public function update(Request $request, Option $option)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'is_quantity' => 'required|boolean',
            'price_type' => 'required|in:per_piece,per_day',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            // 古い画像を削除
            if ($option->image_path) {
                Storage::disk('public')->delete($option->image_path);
            }
            
            $path = $request->file('image')->store('options', 'public');
            $validated['image_path'] = $path;
        }

        $option->update($validated);

        return redirect()
            ->route('admin.options.index')
            ->with('success', 'オプションを更新しました。');
    }

    public function destroy(Option $option)
    {
        if ($option->image_path) {
            Storage::disk('public')->delete($option->image_path);
        }

        $option->delete();

        return redirect()
            ->route('admin.options.index')
            ->with('success', 'オプションを削除しました。');
    }

    /**
     * オプションの並び順を更新
     */
    public function updateSortOrder(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:options,id'
        ]);

        $order = $validated['order'];
        
        // 各オプションの並び順を更新
        foreach ($order as $index => $optionId) {
            Option::where('id', $optionId)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['status' => 'success']);
    }
}
