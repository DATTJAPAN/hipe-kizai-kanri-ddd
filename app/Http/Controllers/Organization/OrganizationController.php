<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization;

use Inertia\Inertia;
use Inertia\Response;

class OrganizationController
{
    public function dashboard(): Response
    {
        return Inertia::render('v1/org/org-dashboard');
    }
}
