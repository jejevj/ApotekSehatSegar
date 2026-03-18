<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;
use App\Models\BillingSetting;

class BillingGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $setting = BillingSetting::where('status', 'aktif')->orderBy('id', 'desc')->first();
            if (!$setting) {
                $setting = BillingSetting::query()->orderBy('id', 'desc')->first();
            }
            if (!$setting || empty($setting->expired_at) || $setting->status === 'sudah_dibayar') {
                return $next($request);
            }

            $expiredAt = $setting->expired_at->timezone('Asia/Jakarta');
            $today = Carbon::now('Asia/Jakarta')->startOfDay();
            $daysLeft = $today->diffInDays($expiredAt->copy()->startOfDay(), false);

            // Share data to all views
            $billingInfo = [
                'expired_at' => $expiredAt->toIso8601String(),
                'days_left' => $daysLeft,
                'jumlah_tagihan' => $setting->jumlah_tagihan,
                'nama_bank' => $setting->nama_bank,
                'no_rek' => $setting->no_rek,
            ];
            View::share('billingInfo', $billingInfo);

            // If expired (H <= 0): don't auto-update DB
            // Allow access only to billing routes; block all others
            if ($daysLeft <= 0) {
                if ($request->routeIs('billing.*') || $request->routeIs('billing.restricted')) {
                    return $next($request);
                }
                if (!$request->routeIs('billing.restricted')) {
                    return redirect()->route('billing.restricted');
                }
            }

            // Redirect handled above
        } catch (\Throwable $e) {
            // Silent fail: do not block app if billing endpoint is unreachable
        }

        return $next($request);
    }

}

