<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login')->withErrors(['auth' => 'Vui lòng đăng nhập tài khoản quản trị.']);
        }
        if (($user->status ?? 'active') === 'banned') {
            auth()->logout();
            return redirect()->route('login')->withErrors(['auth' => 'Tài khoản đã bị khóa.']);
        }
        if (($user->role ?? 'user') !== 'admin') {
            Log::warning('Unauthorized admin access attempt', [
                'user_id' => $user->id, 'ip' => $request->ip(), 'path' => $request->path(),
            ]);
            abort(403, 'Bạn không có quyền truy cập khu vực quản trị.');
        }
        return $next($request);
    }
}
