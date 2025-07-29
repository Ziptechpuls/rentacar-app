<?php

// app/Http/Controllers/Admin/PrivacyController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Policy; // ← モデル名は実際の使用に合わせて

class PrivacyController extends Controller
{
    public function index()
    {
        $policy = Policy::where('type', 'privacy')->first();
        return view('admin.privacy.index', compact('policy'));
    }

    public function edit()
    {
        $policy = Policy::where('type', 'privacy')->first();
        return view('admin.privacy.edit', compact('policy'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $policy = Policy::where('type', 'privacy')->first();

        if (!$policy) {
            // なければ新規作成
            $policy = new Policy();
            $policy->type = 'privacy';
        }

        $policy->content = $request->input('content');
        $policy->save();

        return redirect()->route('admin.privacy.index')->with('success', 'プライバシーポリシーを更新しました');
    }
}
