<?php

namespace App\Http\Middleware;

use App\Models\Retailer;
use Closure;
use Illuminate\Http\Request;

class CheckApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->bearerToken()) {
            return response()->json(['message' => 'api_key is missing', 'status' => 403], 403);
        }

        $retailer = Retailer::where('secret_key', $request->bearerToken())->first();

        if (is_null($retailer)) {
            return response()->json(['message' => 'Retailer not found', 'status' => 404], 404);
        }

        return $next($request);
    }
}
