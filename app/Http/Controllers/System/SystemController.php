<?php

declare(strict_types=1);

namespace App\Http\Controllers\System;

use Inertia\Inertia;
use Inertia\Response;

class SystemController
{
    public function dashboard(): Response
    {
        return Inertia::render('v1/sys/sys-dashboard');
    }
}
