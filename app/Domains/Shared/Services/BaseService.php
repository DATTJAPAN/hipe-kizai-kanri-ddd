<?php

declare(strict_types=1);

namespace App\Domains\Shared\Services;

use App\Domains\Shared\Models\Organization;
use App\Domains\Shared\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Spatie\PrefixedIds\Exceptions\NoPrefixedModelFound;
use Throwable;

/**
 * @template TModel of Model
 * @template TRepository of BaseRepositoryInterface
 * @template TException of \Exception
 */
abstract class BaseService implements BaseServiceInterface
{
    /** @var TModel */
    public $model;

    /** @var TRepository */
    public $repository;

    /** @var class-string<TException> */
    public $exceptionClass;

    /**
     * @param  TModel  $model
     * @param  TRepository  $repository
     * @param  class-string<TException>  $exceptionClass
     */
    public function __construct($model, $repository, $exceptionClass)
    {
        $this->model = $model;
        $this->repository = $repository;
        $this->exceptionClass = $exceptionClass;
    }

    public function showTrashedData(): static
    {
        $this->repository->showTrashedData();

        return $this;
    }

    public function dataTable(): \Illuminate\Database\Eloquent\Collection|array|\Illuminate\Support\Collection
    {
        return $this->repository->dataTable();
    }

    public function findById(int $identifier, ?array $relations = []): ?Model
    {
        return $this->repository->findById($identifier, $relations);
    }

    public function findByPrefixedId(string $identifier, ?array $relations = []): ?Model
    {
        try {
            return $this->repository->findByPrefixedId($identifier, $relations);
        } catch (NoPrefixedModelFound $e) {
            throw $this->exceptionClass::notFound(id: $identifier);
        } catch (Throwable $e) {
            throw $this->exceptionClass::unexpected();
        }

    }

    public function create(array $data): Model
    {
        try {
            $organization = $this->repository->create($data);

            if (! $organization instanceof Organization) {
                throw $this->exceptionClass::createFailed();
            }

            return $organization;
        } catch (QueryException $e) {
            if (in_array($e->errorInfo[0] ?? '', ['23000', '23505'], true)) {
                throw $this->exceptionClass::duplicate();
            }
            throw $this->exceptionClass::unexpected();
        } catch (Throwable $e) {
            throw $this->exceptionClass::unexpected();
        }
    }

    public function updateByIdOrPrefixedId(int|string $identifier, array $data): Model
    {
        try {
            $org = $this->repository->updateByIdOrPrefixedId(identifier: $identifier, data: $data);
            $org instanceof Organization ?: throw $this->exceptionClass::updateFailed();

            return $org;

        } catch (QueryException $e) {
            if ('23000' === $e->errorInfo[0] || '23505' === $e->errorInfo[0]) {
                throw $this->exceptionClass::duplicate();
            }
            throw $this->exceptionClass::unexpected();
        } catch (Throwable $e) {
            throw $this->exceptionClass::unexpected();
        }
    }

    public function deleteByIdOrPrefixedId(int|string $identifier): bool
    {
        try {
            $result = $this->repository->deleteByIdOrPrefixedId(identifier: $identifier);

            return ! $result ? throw $this->exceptionClass::deleteFailed() : $result;

        } catch (Throwable $e) {
            throw $this->exceptionClass::unexpected();
        }
    }

    public function restoreByIdOrPrefixedId(int|string $identifier): bool
    {
        try {
            $result = $this->repository->restoreByIdOrPrefixedId(identifier: $identifier);

            return ! $result ? throw $this->exceptionClass::restoreFailed() : $result;
        } catch (Throwable $e) {
            throw $this->exceptionClass::unexpected();
        }
    }
}
