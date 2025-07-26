<?php

declare(strict_types=1);

namespace App\Domains\System\Organizations;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use App\Domains\System\Organizations\OrganizationData;
use Illuminate\Http\Request;

class OrganizationController
{
    public function __construct(private OrganizationService $service) {}

    public function dashboard(): Response
    {
        return Inertia::render('v1/sys/org/dashboard');
    }

    public function add(): Response
    {
        return Inertia::render('v1/sys/org/manage-org');
    }

    public function manage(string $prefixedId): Response
    {
        return Inertia::render('v1/sys/org/manage-org');
    }

    public function addHandler(OrganizationData $data)
    {
        try {
            dd($data);
        } catch (Exception $e) {
            logger()->error('Error adding organization: ' . $e->getMessage());
        }
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
                ['message' => $e->getMessage()]
            );
        }
    }
}
