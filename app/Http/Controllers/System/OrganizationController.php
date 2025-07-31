<?php

declare(strict_types=1);

namespace App\Http\Controllers\System;

use App\Domains\Shared\Data\Request\DatatableRequestData;
use App\Domains\Shared\Data\Request\ModelRequestData;
use App\Domains\Shared\Data\Response\ResponseFormData;
use App\Domains\System\Organizations\OrganizationException;
use App\Domains\System\Organizations\OrganizationFormData;
use App\Domains\System\Organizations\OrganizationResourceData;
use App\Domains\System\Organizations\OrganizationService;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

readonly class OrganizationController
{
    public function __construct(private OrganizationService $service) {}

    public function dashboard(): Response
    {
        return Inertia::render('v1/sys/org/dashboard');
    }

    public function manage(Request $request, ?string $prefixedId = null): Response
    {
        // ---> We assume it's for creation
        if (is_null($prefixedId)) {
            return Inertia::render(
                component: 'v1/sys/org/manage-org',
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
                component: 'v1/sys/org/manage-org',
                props: [
                    'context' => [
                        'form' => ResponseFormData::forPrefixed(
                            data: OrganizationResourceData::from($model)->toArray(),
                            id: $model?->prefixed_id
                        )->toArray(),
                    ],
                ]
            );
        } catch (OrganizationException $e) {
            logger()?->error($e->getMessage(), ['prefixed_id' => $prefixedId]);

            return Inertia::render(
                component: 'v1/sys/org/manage-org',
                props: [
                    'context' => [
                        'form' => ResponseFormData::forMissingManage()->toArray(),
                        'error' => $e->getMessage(),
                    ],
                ]
            );
        } catch (Exception $e) {
            logger()?->error('Unexpected error while loading organization: '.$e->getMessage(), ['prefixed_id' => $prefixedId]);

            return Inertia::render(
                component: 'v1/sys/org/manage-org',
                props: [
                    'context' => [
                        'form' => ResponseFormData::forUnknown()->toArray(),
                        'error' => 'An unexpected error occurred while loading the organization.',
                    ],
                ]
            );
        }
    }

    public function addHandler(OrganizationFormData $data): \Illuminate\Http\RedirectResponse
    {
        try {
            $organization = $this->service->create($data->toArray());

            return to_route(
                'v1.sys.orgs.manage:get',
                ['prefixedId' => $organization->prefixed_id]
            )->with('success', 'Organization created successfully!');
        } catch (Exception $e) {
            logger()?->error('Error adding organization: '.$e->getMessage());

            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    public function updateHandler(string $prefixedId, OrganizationFormData $data): \Illuminate\Http\RedirectResponse
    {
        try {
            $organization = $this->service->updateByIdOrPrefixedId($prefixedId, $data->toArray());

            // Todo: update all the user email domain if the company change domain to avoid duplication

            return to_route(
                'v1.sys.orgs.manage:get',
                ['prefixedId' => $organization->prefixed_id]
            )->with('success', 'Organization created successfully!');
        } catch (Exception $e) {
            logger()?->error('Error: '.$e->getMessage());

            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    public function softDeleteHandler(string $prefixedId): \Illuminate\Http\RedirectResponse
    {
        try {
            $this->service->deleteByIdOrPrefixedId(identifier: $prefixedId);

            return to_route('v1.sys.orgs.manage:get', [
                'prefixedId' => $prefixedId,
                'trashed' => true,
            ])->with('success', 'Organization deleted successfully!');
        } catch (Exception $e) {
            logger()?->error('Error deleted organization: '.$e->getMessage());

            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function restoreHandler(string $prefixedId): \Illuminate\Http\RedirectResponse
    {
        try {
            $this->service->repository->showOnlyTrashedData();
            $this->service->restoreByIdOrPrefixedId(identifier: $prefixedId);

            return to_route('v1.sys.orgs.manage:get', ['prefixedId' => $prefixedId])->with('success', 'Organization restoring successfully!');
        } catch (Exception $e) {
            logger()?->error('Error restoring organization: '.$e->getMessage());

            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function forceDeleteHandler(string $prefixedId): \Illuminate\Http\RedirectResponse
    {
        try {
            $this->service->repository->showOnlyTrashedData();
            $this->service->repository->deletePermanently();
            $result = $this->service->deleteByIdOrPrefixedId(identifier: $prefixedId);

            return to_route('v1.sys.orgs.manage:get', [
                'prefixedId' => $prefixedId,
                'trashed' => true,
                'forceDeleted' => $result,
            ])->with('success', 'Organization force deleted successfully!');
        } catch (Exception $e) {
            logger()?->error('Error force deleting organization: '.$e->getMessage());

            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
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
