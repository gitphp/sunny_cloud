<?php

namespace App\Http\Middleware;

use App\Constants\Code\UserError;
use App\Helpers\ApiResponseHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureBackendAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('backend')->check()) {
            if ($request->expectsJson() || $request->is('backend/api/*')) {
                return ApiResponseHelper::error(UserError::AUTH_NOT_LOGGED_IN, '未登录');
            }

            return redirect('/backend/login');
        }

        return $next($request);
    }
}
