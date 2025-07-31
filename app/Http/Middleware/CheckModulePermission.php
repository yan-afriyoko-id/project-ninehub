<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Module;

class CheckModulePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // If no specific permission is required, just check if user is authenticated
        if (!$permission) {
            return $next($request);
        }

        // Check if user has the required permission
        if (!$user->hasPermissionTo($permission)) {
            return response()->json([
                'message' => 'Access denied. You do not have permission to access this resource.'
            ], 403);
        }

        return $next($request);
    }
}
