<?php

declare(strict_types=1);

namespace App\Http\Controllers\Organization;

use App\Domains\Organization\Tags\OrganizationTagFormData;
use App\Domains\Organization\Tags\OrganizationTagOptionData;
use App\Domains\Organization\Tags\OrganizationTagResourceData;
use App\Domains\Organization\Tags\OrganizationTagService;
use App\Domains\Organization\Units\OrganizationUnitException;
use App\Domains\Shared\Data\Request\DatatableRequestData;
use App\Domains\Shared\Data\Request\ModelRequestData;
use App\Domains\Shared\Data\Request\OptionRequestData;
use App\Domains\Shared\Data\Response\ResponseFormData;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class OrganizationTagController
{
    private string $componentDashboard = 'v1/org/tag/dashboard';

    private string $componentManagement = 'v1/org/tag/manage-tag';

    private string $handlerRedirectTo = 'v1.org.tags.manage:get';

    public function __construct(private OrganizationTagService $service) {}

    public function dashboard(): Response
    {
        return inertia()->render($this->componentDashboard);
    }

    public function manage(Request $request, ?string $prefixedId = null): Response
    {
        // ---> We assume it's for creation
        if (is_null($prefixedId)) {
            return inertia()->render($this->componentManagement, [
                'context' => [
                    'form' => ResponseFormData::forCreate()->toArray(),
                ],
            ]);
        }

        // ---> We assume it's for update
        try {
            $requestData = ModelRequestData::fromRequest(request: $request);

            if ($requestData->trashed) {
                $this->service->repository->showTrashedData();
            }

            $model = $this->service->findByPrefixedId(identifier: $prefixedId);

            return inertia()->render($this->componentManagement, [
                'context' => [
                    'form' => ResponseFormData::forPrefixed(
                        data: OrganizationTagResourceData::from($model)->toArray(),
                        id: $model?->prefixed_id
                    )->toArray(),
                ],
            ]);
        } catch (OrganizationUnitException $e) {
            logger()?->error($e->getMessage(), ['prefixed_id' => $prefixedId]);

            return inertia()->render($this->componentManagement, [
                'context' => [
                    'form' => ResponseFormData::forMissingManage()->toArray(),
                    'error' => $e->getMessage(),
                ],
            ]);
        } catch (Exception $e) {
            logger()?->error('Unexpected error: '.$e->getMessage(), ['prefixed_id' => $prefixedId]);

            return inertia()->render($this->componentManagement, [
                'context' => [
                    'form' => ResponseFormData::forMissingManage()->toArray(),
                    'error' => $e->getMessage(),
                ],
            ]);
        }
    }

    public function addHandler(OrganizationTagFormData $data): ?RedirectResponse
    {
        try {
            $model = $this->service->create($data->toArray());

            return to_route(
                $this->handlerRedirectTo,
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

    public function updateHandler(string $prefixedId, OrganizationTagFormData $data): ?RedirectResponse
    {
        try {
            $model = $this->service->updateByIdOrPrefixedId($prefixedId, $data->toArray());

            return to_route(
                $this->handlerRedirectTo,
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

            return to_route($this->handlerRedirectTo, [
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
            $result = $this->service->dataTable();

            return successResponseJson([
                'data' => OrganizationTagResourceData::collect(items: $result),
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
                'data' => OrganizationTagOptionData::fromCollection($result),
            ]);
        } catch (Exception $e) {
            return errorResponseJson(
                ['message' => $e->getMessage()]
            );
        }
    }
}
