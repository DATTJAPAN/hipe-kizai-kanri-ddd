<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization;

use App\Domains\Organization\Units\OrganizationUnitException;
use App\Domains\Organization\Units\OrganizationUnitFormData;
use App\Domains\Organization\Units\OrganizationUnitOptionData;
use App\Domains\Organization\Units\OrganizationUnitResourceData;
use App\Domains\Organization\Units\OrganizationUnitService;
use App\Domains\Shared\Data\Request\DatatableRequestData;
use App\Domains\Shared\Data\Request\ModelRequestData;
use App\Domains\Shared\Data\Request\OptionRequestData;
use App\Domains\Shared\Data\Response\ResponseFormData;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationUnitController
{
    public function __construct(private OrganizationUnitService $service) {}

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

        // ---> We assume it's for update
        try {
            $requestData = ModelRequestData::fromRequest(request: $request);

            if ($requestData->trashed) {
                $this->service->repository->showTrashedData();
            }

            $model = $this->service->findByPrefixedId(identifier: $prefixedId);

            return Inertia::render(
                component: $inertiaPage,
                props: [
                    'context' => [
                        'form' => ResponseFormData::forPrefixed(
                            data: OrganizationUnitResourceData::from($model)->toArray(),
                            id: $model?->prefixed_id
                        )->toArray(),
                    ],
                ]
            );
        } catch (OrganizationUnitException $e) {
            logger()?->error($e->getMessage(), ['prefixed_id' => $prefixedId]);

            return Inertia::render(
                component: $inertiaPage,
                props: [
                    'context' => [
                        'form' => ResponseFormData::forMissingManage()->toArray(),
                        'error' => $e->getMessage(),
                    ],
                ]
            );
        } catch (Exception $e) {
            logger()?->error('Unexpected error: '.$e->getMessage(), ['prefixed_id' => $prefixedId]);

            return Inertia::render(
                component: $inertiaPage,
                props: [
                    'context' => [
                        'form' => ResponseFormData::forUnknown()->toArray(),
                        'error' => 'An unexpected error occurred',
                    ],
                ]
            );
        }
    }

    public function addHandler(OrganizationUnitFormData $data): ?RedirectResponse
    {
        try {
            $model = $this->service->create($data->toArray());

            return to_route(
                'v1.org.units.manage:get',
                ['prefixedId' => $model->prefixedId]
            )->with('success', 'Created Successfully');
        } catch (Exception $e) {
            logger()?->error('Error: '.$e->getMessage());

            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    public function updateHandler(string $prefixedId, OrganizationUnitFormData $data): ?RedirectResponse
    {
        try {
            $model = $this->service->updateByIdOrPrefixedId($prefixedId, $data->toArray());

            return to_route(
                'v1.org.units.manage:get',
                ['prefixedId' => $model->prefixedId]
            )->with('success', 'Created Successfully');
        } catch (Exception $e) {
            logger()?->error('Error: '.$e->getMessage());

            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    public function forceDeleteHandler(string $prefixedId): RedirectResponse
    {
        try {
            $this->service->repository->showOnlyTrashedData();
            $this->service->repository->deletePermanently();
            $result = $this->service->deleteByIdOrPrefixedId(identifier: $prefixedId);

            return to_route('v1.org.units.manage:get', [
                'prefixedId' => $prefixedId,
                'forceDeleted' => $result,
            ])->with('success', 'Deleted successfully!');
        } catch (Exception $e) {
            logger()?->error('Error: '.$e->getMessage());

            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function datatable(DatatableRequestData $requestData): JsonResponse
    {
        try {

            if ($requestData->onlyTrashed) {
                $this->service->repository->showOnlyTrashedData();
            }

            if ($requestData->withTrashed) {
                $this->service->repository->showTrashedData();
            }

            $this->service->repository->tapQueryOnce(
                fn ($query) => $query
                    ->with('parentUnit')
            );

            $result = $this->service->dataTable();

            return successResponseJson([
                'data' => OrganizationUnitResourceData::collect(items: $result),
            ]);
        } catch (Exception $e) {
            return errorResponseJson(
                ['message' => $e->getMessage()]
            );
        }
    }

    public function options(OptionRequestData $requestData): JsonResponse
    {
        try {
            $this->service->repository->tapQueryOnce(function ($query) use ($requestData) {
                foreach ($requestData->exclude ?? [] as $column => $value) {
                    $query->where($column, '!=', $value);
                }

                $query = applyScopesToQuery($query, $requestData->scopes);

                return $query;
            });

            $result = $this->service->dataTable();

            return successResponseJson([
                'data' => OrganizationUnitOptionData::fromCollection($result),
            ]);
        } catch (Exception $e) {
            return errorResponseJson(
                ['message' => $e->getMessage()]
            );
        }
    }
}
