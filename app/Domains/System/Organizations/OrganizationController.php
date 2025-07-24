<?php

declare(strict_types=1);

namespace App\Domains\System\Organizations;

use Exception;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationController
{
    public function __construct(private OrganizationService $service) {}

    public function dashboard(): Response
    {
        return Inertia::render('v1/sys/org/dashboard');
    }

    public function getAll()
    {
        try {
            $this->service->getAll();

            return successResponseJson([
                'data' => $this->service->getAll(),
            ]);
        } catch (Exception $e) {
            return errorResponseJson(
                ['message' => $e->getMessage()],
                httpStatus: $e->getStatusCode() ?? 403
            );
        }
    }
}
