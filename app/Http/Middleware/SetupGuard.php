<?php

namespace App\Http\Middleware;

use App\Services\BusinessConfigService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetupGuard
{
    public function __construct(private BusinessConfigService $config) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Bypass untuk super_admin
        if (\App\Services\StoreContext::isSuperAdmin()) {
            return $next($request);
        }

        // Skip check for setup routes themselves
        if ($request->is('setup') || $request->is('setup/*')) {
            return $next($request);
        }

        if (!$this->config->isSetupComplete()) {
            return redirect()->route('setup.index');
        }

        return $next($request);
    }
}
