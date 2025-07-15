<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class XmlHttpRequestOnlyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->ajax()) {
            abort(403, 'This action is not allowed.');
        }

        return $next($request);
    }
}
