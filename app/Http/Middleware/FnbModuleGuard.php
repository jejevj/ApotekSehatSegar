<?php

namespace App\Http\Middleware;

use App\Services\BusinessConfigService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FnbModuleGuard
{
    public function __construct(private BusinessConfigService $config) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->config->get('business_type') !== 'fnb') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Modul FnB tidak aktif untuk bisnis ini.'], 403);
            }
            return redirect('/')->with('error', 'Modul FnB tidak aktif untuk bisnis ini.');
        }

        return $next($request);
    }
}
