<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureCompanyAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $admin = Auth::guard('admin')->user();
        $companyId = $request->route('company');

        // URLのcompany_idとログインユーザーのcompany_idが一致するか確認
        if ($admin->company_id != $companyId) {
            abort(403, '他社のデータにはアクセスできません。');
        }

        // 後続の処理でcompany_idを使用できるようにセッションに保存
        session(['current_company_id' => $admin->company_id]);

        return $next($request);
    }
} 