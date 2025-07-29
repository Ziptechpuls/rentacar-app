<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        
        // 管理者用ルートの場合はスキップ
        if ($request->is('admin*')) {
            return $next($request);
        }

        // サブドメインを取得
        $subdomain = explode('.', $host)[0];

        // ローカル開発環境の場合
        if (app()->environment('local')) {
            if ($subdomain === 'localhost') {
                return redirect()->route('admin.register');
            }
            $company = Company::where('subdomain', $subdomain)->first();
        } else {
            // 本番環境の場合
            if ($subdomain === 'rensystem') {
                return redirect()->route('admin.register');
            }
            $company = Company::where('subdomain', $subdomain)->first();
        }

        if (!$company) {
            // 会社が見つからない場合は管理画面にリダイレクト
            return redirect()->route('admin.register');
        }

        // 会社情報をコンテナにバインド
        app()->instance('company', $company);
        
        // ビューで使用できるようにグローバル変数として設定
        view()->share('company', $company);

        return $next($request);
    }
} 