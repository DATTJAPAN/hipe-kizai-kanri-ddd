<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class HandleAppearance
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // TODO: implement setting of cookie in front end and save this as a preference in user settings
        View::share('appearance', $request->cookie('appearance') ?? 'system');

        // TODO: add language support

        // TODO: add data table last state

        return $next($request);
    }
}
