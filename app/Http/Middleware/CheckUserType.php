<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserType
{
    public function handle(Request $request, Closure $next, ...$types)
    {
        // Get authenticated user
        $user = $request->user();

        // Check if user exists and their type is allowed
        if ($user && in_array($user->type, $types)) {
            return $next($request);
        }

        return response()->json(['status' => 'fail','data'=>null ,'message'=>'messages.unauthorized'])->setStatusCode(401);
    }
}
