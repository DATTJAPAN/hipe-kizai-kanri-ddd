<?php

declare(strict_types=1);

namespace App\Domains\System\Organizations;

use App\Domains\Shared\Models\Organization;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use InvalidArgumentException;
use Throwable;

readonly class OrganizationService
{
    private OrganizationRepository $repository;

    private OrganizationException $exception;

    private Organization $modelInstance;

    public function __construct()
    {
        $this->modelInstance = new Organization();
        $this->repository = new OrganizationRepository($this->modelInstance);
        $this->exception = new OrganizationException();
    }

    public function getAll(): Collection
    {
        try {
            return $this->repository->getList();
        } catch (Throwable $e) {
            throw $this->exception::unexpected($e->getMessage());
        }
    }

    public function findByPrefixedId(string $prefix): ?Organization
    {
        try {
            return $this->repository->getByPrefixedId($prefix);
        } catch (Throwable $e) {
            throw $this->exception::unexpected($e->getMessage());
        }
    }

    public function create(array $data): Organization
    {
        try {
            $organization = $this->repository->create($data);

            if (! $organization instanceof Organization) {
                throw $this->exception::createFailed();
            }

            return $organization;
        } catch (QueryException $e) {
            if ('23000' === $e->errorInfo[0] || '23505' === $e->errorInfo[0]) {
                throw $this->exception::duplicate();
            }

            throw $this->exception::unexpected();
        } catch (Throwable $e) {
            throw $this->exception::unexpected();
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
                throw $this->exception::updateFailed();
            }

            return $organization;
        } catch (ModelNotFoundException) {
            throw $this->exception::notFound($id);
        } catch (InvalidArgumentException $e) {
            throw $this->exception::invalidData($e->getMessage());
        } catch (QueryException $e) {
            if ('23000' === $e->errorInfo[0] || '23505' === $e->errorInfo[0]) {
                throw $this->exception::duplicate();
            }
            throw $this->exception::unexpected();
        } catch (Throwable $e) {
            throw $this->exception::unexpected($e->getMessage());
        }
    }

    public function updateByPrefixedId(string $prefixedId, array $data): Organization
    {
        try {
            if (empty($data)) {
                throw new InvalidArgumentException('Update data cannot be empty.');
            }

            $organization = $this->repository->updateByPrefixedId($prefixedId, $data);

            if (! $organization instanceof Organization) {
                throw $this->exception::updateFailed();
            }

            return $organization;
        } catch (ModelNotFoundException) {
            throw $this->exception::notFound($prefixedId);
        } catch (InvalidArgumentException $e) {
            throw $this->exception::invalidData($e->getMessage());
        } catch (QueryException $e) {
            if ('23000' === $e->errorInfo[0] || '23505' === $e->errorInfo[0]) {
                throw $this->exception::duplicate();
            }
            throw $this->exception::unexpected();
        } catch (Throwable $e) {
            throw $this->exception::unexpected($e->getMessage());
        }
    }

    public function delete(int $id): bool
    {
        try {
            $deleted = $this->repository->delete($id);

            if (! $deleted) {
                throw $this->exception::deleteFailed();
            }

            return true;
        } catch (ModelNotFoundException) {
            throw $this->exception::notFound($id);
        } catch (Throwable $e) {
            throw $this->exception::unexpected($e->getMessage());
        }
    }
}
