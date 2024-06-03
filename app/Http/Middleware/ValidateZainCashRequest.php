<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateZainCashRequest
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
        $request->validate([
            'amount' => 'required|integer|min:250',
            'service_type' => 'required|string',
            'order_id' => 'required|string'
        ]);

        return $next($request);
    }
}
