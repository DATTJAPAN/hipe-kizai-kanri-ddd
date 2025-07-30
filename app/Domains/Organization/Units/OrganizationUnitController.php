<?php

declare(strict_types=1);

namespace App\Domains\Organization\Units;

use App\Domains\Shared\Data\Request\DatatableRequestData;
use App\Domains\Shared\Data\Response\ResponseFormData;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Exception;

class OrganizationUnitController
{
    public function __construct(private OrganizationUnitService $service)
    {
    }

    public function dashboard(): Response
    {
        return Inertia::render(component: 'v1/org/unit/dashboard');
    }

    public function manage(Request $request, ?string $prefixedId = null): Response
    {
        $inertiaPage = 'v1/org/unit/manage-unit';

        // ---> We assume it's for creation
        if (is_null($prefixedId)) {
            return Inertia::render(
                component: $inertiaPage,
                props: [
                    'context' => [
                        'form' => ResponseFormData::forCreate()->toArray(),
                    ],
                ]
            );
        }


        return Inertia::render($inertiaPage);
    }

    public function datatable(DatatableRequestData $requestData): \Illuminate\Http\JsonResponse
    {
        try {

            if ($requestData->onlyTrashed) {
                $this->service->repository->showOnlyTrashedData();
            }

            if ($requestData->withTrashed) {
                $this->service->repository->showTrashedData();
            }

            $result = $this->service->dataTable();

            return successResponseJson([
                'data' => $result,
            ]);
        } catch (Exception $e) {
            return errorResponseJson(
                ['message' => $e->getMessage()]
            );
        }
    }
}
