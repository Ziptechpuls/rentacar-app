<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ResolveCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        
        // 管理者用ルートの場合の処理
        if ($request->is('admin*') || $request->is('admin/*') || str_starts_with($request->path(), 'admin')) {
            // サブドメインを取得
            $subdomain = explode('.', $host)[0];
            
            \Log::info("ResolveCompany: Admin route detected", [
                'host' => $host,
                'subdomain' => $subdomain,
                'path' => $request->path()
            ]);
            
            // adminサブドメインの場合はスーパー管理者用
            if ($subdomain === 'admin') {
                $company = Company::where('subdomain', 'admin')->first();
                if ($company) {
                    app()->instance('company', $company);
                    view()->share('company', $company);
                }
                \Log::info("ResolveCompany: Admin subdomain processed");
                return $next($request);
            }
            
            // その他のサブドメイン（test, rentacarなど）の場合はテナント管理者用
            $company = Company::where('subdomain', $subdomain)->first();
            if ($company) {
                app()->instance('company', $company);
                view()->share('company', $company);
                \Log::info("ResolveCompany: Tenant admin processed", ['company' => $company->name]);
                return $next($request);
            }
            
            \Log::warning("ResolveCompany: No company found for subdomain", ['subdomain' => $subdomain]);
            // 会社が見つからない場合は管理画面にリダイレクト
            return redirect()->route('admin.register');
        }

        // 一般ユーザー用ルートの処理
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