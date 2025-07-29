<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Policy;

class TermsController extends Controller
{
    public function index()
    {
        $policy = Policy::where('type', 'terms')->first();
        return view('admin.terms.index', compact('policy'));
    }

    public function edit()
    {
        $policy = Policy::where('type', 'terms')->first();
        return view('admin.terms.edit', compact('policy'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $policy = Policy::where('type', 'terms')->first();

        if (!$policy) {
            // なければ新規作成
            $policy = new Policy();
            $policy->type = 'terms';
        }

        $policy->content = $request->input('content');
        $policy->save();

        return redirect()->route('admin.terms.index')->with('success', '利用規約を更新しました');
    }
}
