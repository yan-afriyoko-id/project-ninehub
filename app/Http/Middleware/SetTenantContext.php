<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;
use Stancl\Tenancy\Tenancy;

class SetTenantContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if ($user && $user->tenant_id) {
            $tenant = Tenant::findOrFail($user->tenant_id);
            tenancy()->initialize($tenant);
        }

        return $next($request);
    }
}
