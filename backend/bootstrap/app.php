<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ロードバランサ / リバースプロキシ配下で HTTPS・接続元 IP を正しく検出する。
        // TRUSTED_PROXIES に信頼するプロキシの IP/CIDR をカンマ区切りで指定（'*' で全て信頼）。
        // 未設定なら信頼しない（直接公開時に X-Forwarded-* を偽装されないため）。
        if ($proxies = env('TRUSTED_PROXIES')) {
            $middleware->trustProxies(at: $proxies === '*' ? '*' : array_map('trim', explode(',', $proxies)));
        }

        $middleware->alias([
            'auth.admin' => \App\Http\Middleware\AuthAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
