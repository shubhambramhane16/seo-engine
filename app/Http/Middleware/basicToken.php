<?php

namespace App\Http\Middleware;

use Closure;
use Response;

class basicToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->header('Token')) {
            if ($request->header('Token') == env('BASIC_TOKEN')) {
                return $next($request);
            } else {
                $result['Result'] = [];
                $result['Success'] = 'False';
                $result['Message'] = 'Token is missing or invalid in headers.';
                return response()->json($result);
            }
        } else {
            $result['Result'] = [];
            $result['Success'] = 'False';
            $result['Message'] = 'Token is missing in headers.';
            return response()->json($result);
        }
    }
}
