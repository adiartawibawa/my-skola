<?php

namespace App\Http\Middleware;

use App\Settings\AppSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $settings = app(AppSettings::class);

        if (! $settings->maintenance_mode) {
            return $next($request);
        }

        // Panel admin tetap harus bisa diakses — supaya admin bisa
        // mematikan mode pemeliharaan lagi dari sana.
        if ($request->is('admin*')) {
            return $next($request);
        }

        // Admin yang sedang login tetap bisa browsing situs publik untuk pratinjau.
        if (auth()->check() && auth()->user()->role->isAdmin()) {
            return $next($request);
        }

        return response()->view('maintenance', [
            'message' => $settings->maintenance_message,
        ], 503);
    }
}
