<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LicenseCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
//    public function handle(Request $request, Closure $next): Response
//    {
//        return $next($request);
//    }
    public function handle($request, Closure $next) {
        $file = storage_path('license.dat');
        if (!file_exists($file)) return redirect('/activate');

        $stored = file_get_contents($file);
        $current = trim(shell_exec('wmic diskdrive get serialnumber 2>&1'));
        $hwid = hash('sha256', preg_replace('/\s+/', ' ', $current));

        if ($stored !== $hwid) abort(403, 'Invalid license!');
        return $next($request);
    }

}
