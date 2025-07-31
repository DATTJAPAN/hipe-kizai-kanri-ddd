<?php

declare(strict_types=1);

namespace App\Domains\Shared\Authorization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use InvalidArgumentException;
use Throwable;

class RoleService
{
    private RoleRepository $repository;

    private RoleException $exception;

    public function __construct()
    {
        $this->repository = new RoleRepository(new Role());
        $this->exception = new RoleException();
    }

    /**
     * @throws RoleException
     */
    public function createUsingBuilder(RoleBuilder $builder): Role
    {
        return $this->create($builder->build()->toArray());
    }

    /**
     * @throws RoleException
     */
    public function create(array $data): ?Role
    {
        try {
            $created = $this->repository->create($data);

            if (! $created instanceof Role) {
                throw $this->exception::createFailed();
            }

            return $created;

        } catch (InvalidArgumentException $e) {
            throw $this->exception::invalidData($e->getMessage());
        } catch (ModelNotFoundException $e) {
            throw $this->exception::createFailed($e->getMessage());
        } catch (QueryException $e) {
            if (23505 === $e->errorInfo[1]) {
                throw $this->exception::duplicate(
                    name: $data['name'] ?? 'unknown',
                    guard: $data['guard_name'] ?? 'unknown'
                );
            }
            throw $this->exception::unexpected($e->getMessage());
        } catch (Throwable $e) {
            throw $this->exception::unexpected($e->getMessage());
        }
    }

    /**
     * @throws RoleException
     */
    public function update(Model|int $modelOrId, array $data): Model
    {
        try {
            if (is_int($modelOrId)) {
                return $this->repository->update($modelOrId, $data);
            }

            return $this->repository->updateModel($modelOrId, $data);
        } catch (ModelNotFoundException $e) {
            throw $this->exception::updateFailed($e->getMessage());
        } catch (Throwable $e) {
            throw $this->exception::unexpected($e->getMessage());
        }
    }

    /**
     * @throws RoleException
     */
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
