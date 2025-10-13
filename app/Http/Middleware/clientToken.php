<?php

namespace App\Http\Middleware;

use Closure;
use Response;
use Config;

class clientToken
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
            if (($request->header('Token') == config('api.client_token'))) {
                return $next($request);
            } else {
                $result['Success'] = 'False';
                $result['Message'] = 'Client Token is missing or invalid in headers.';
                return  response()->json($result);
            }
        } else {
            $result['Success'] = 'False';
            $result['Message'] = 'Client Token is missing in headers.';
            return response()->json($result);
        }
    }
}
