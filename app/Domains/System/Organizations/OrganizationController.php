<?php

declare(strict_types=1);

namespace App\Domains\System\Organizations;

use App\Domains\Shared\Data\Response\ResponseFormData;
use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\PrefixedIds\Exceptions\NoPrefixedModelFound;

class OrganizationController
{
    public function __construct(private OrganizationService $service)
    {
    }

    public function dashboard(): Response
    {
        return Inertia::render('v1/sys/org/dashboard');
    }

    public function manage(?string $prefixedId = null): Response
    {
        // ---> We assume it's for create
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
            $model = $this->service->findByPrefixedId($prefixedId);

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
            logger()?->error('Unexpected error while loading organization: ' . $e->getMessage(), ['prefixed_id' => $prefixedId]);

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
            logger()->error('Error adding organization: ' . $e->getMessage());
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
            logger()->error('Error adding organization: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    public function deactivateHandler(string $prefixedId): \Illuminate\Http\RedirectResponse
    {
        try {
            $this->service->deleteByIdOrPrefixedId(identifier: $prefixedId);

            return to_route('v1.sys.orgs.manage:get', ['prefixedId' => $prefixedId])->with('success', 'Organization deactivated successfully!');
        } catch (Exception $e) {
            logger()->error('Error deactivating organization: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function datatable()
    {
        try {
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
