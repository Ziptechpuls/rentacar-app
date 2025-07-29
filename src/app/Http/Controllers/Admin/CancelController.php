<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Policy;

class CancelController extends Controller
{
    public function index()
    {
        $policy = Policy::where('type', 'cancel')->first();
        return view('admin.cancel.index', compact('policy'));
    }

    public function edit()
    {
        $policy = Policy::where('type', 'cancel')->first();
        return view('admin.cancel.edit', compact('policy'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $policy = Policy::where('type', 'cancel')->first();

        if (!$policy) {
            // なければ新規作成
            $policy = new Policy();
            $policy->type = 'cancel';
        }

        $policy->content = $request->input('content');
        $policy->save();

        return redirect()->route('admin.cancel.index')->with('success', 'キャンセルポリシーを更新しました');
    }
}
