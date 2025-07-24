<?php

declare(strict_types=1);

namespace App\Domains\System\Organizations;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;
use Throwable;

readonly class OrganizationService
{
    private OrganizationRepository $repository;

    public function __construct()
    {
        $this->repository = new OrganizationRepository(new Organization());
    }

    public function getAll(): Collection
    {
        try {
            return $this->repository->getList();
        } catch (Throwable $e) {
            throw OrganizationException::unexpected($e->getMessage());
        }
    }

    public function create(array $data): Organization
    {
        try {
            $organization = $this->repository->create($data);

            if (! $organization instanceof Organization) {
                throw OrganizationException::createFailed();
            }

            return $organization;
        } catch (InvalidArgumentException $e) {
            throw OrganizationException::invalidData($e->getMessage());
        } catch (ModelNotFoundException $e) {
            throw OrganizationException::createFailed($e->getMessage());
        } catch (Throwable $e) {
            throw OrganizationException::unexpected($e->getMessage());
        }
    }

    public function update(int $id, array $data): Organization
    {
        try {
            if (empty($data)) {
                throw new InvalidArgumentException('Update data cannot be empty.');
            }

            $organization = $this->repository->update($id, $data);

            if (! $organization instanceof Organization) {
                throw OrganizationException::updateFailed();
            }

            return $organization;
        } catch (ModelNotFoundException) {
            throw OrganizationException::notFound($id);
        } catch (InvalidArgumentException $e) {
            throw OrganizationException::invalidData($e->getMessage());
        } catch (Throwable $e) {
            throw OrganizationException::unexpected($e->getMessage());
        }
    }

    public function delete(int $id): bool
    {
        try {
            $deleted = $this->repository->delete($id);

            if (! $deleted) {
                throw OrganizationException::deleteFailed();
            }

            return true;
        } catch (ModelNotFoundException) {
            throw OrganizationException::notFound($id);
        } catch (Throwable $e) {
            throw OrganizationException::unexpected($e->getMessage());
        }
    }
}
