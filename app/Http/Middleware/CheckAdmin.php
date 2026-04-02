<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();
        if ($user->role !== 'admin') {
            Auth::logout();
            return redirect()->route('admin.login')->with('error', 'Bạn không có quyền truy cập trang quản trị.');
        }

        return $next($request);
    }
}
