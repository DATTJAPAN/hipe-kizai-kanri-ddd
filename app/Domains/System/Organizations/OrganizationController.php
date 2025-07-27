<?php

declare(strict_types=1);

namespace App\Domains\System\Organizations;

use App\Domains\Shared\Data\Response\ResponseFormData;
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

    public function manage(?string $prefixedId = null): Response
    {
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

        try {
            $model = $this->service->findByPrefixedId($prefixedId);

            return Inertia::render(
                component: 'v1/sys/org/manage-org',
                props: [
                    'context' => [
                        'form' => ResponseFormData::forPrefixed(
                            data: OrganizationData::from($model)->toArray(),
                            id: $model?->prefixed_id
                        )->toArray(),
                    ],
                ]
            );
        } catch (OrganizationException $e) {
            logger()?->error('Organization not found: '.$e->getMessage(), ['prefixed_id' => $prefixedId]);

            return Inertia::render(
                component: 'v1/sys/org/manage-org',
                props: [
                    'context' => [
                        'form' => ResponseFormData::forMissingManage()->toArray(),
                        'error' => 'Organization not found.',
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

    public function addHandler(OrganizationData $data): \Illuminate\Http\RedirectResponse
    {
        try {
            $organization = $this->service->create($data->toArray());

            return to_route('v1.sys.orgs.manage:get',
                ['prefixedId' => $organization->prefixed_id]
            )->with('success', 'Organization created successfully!');
        } catch (Exception $e) {
            logger()->error('Error adding organization: '.$e->getMessage());

            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    public function updateHandler(string $prefixedId, OrganizationData $data): \Illuminate\Http\RedirectResponse
    {
        try {
            $organization = $this->service->updateByPrefixedId($prefixedId, $data->toArray());

            return to_route('v1.sys.orgs.manage:get',
                ['prefixedId' => $organization->prefixed_id]
            )->with('success', 'Organization created successfully!');
        } catch (Exception $e) {
            logger()->error('Error adding organization: '.$e->getMessage());

            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
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
