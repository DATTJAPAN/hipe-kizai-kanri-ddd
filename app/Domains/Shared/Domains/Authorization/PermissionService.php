<?php

declare(strict_types=1);

namespace App\Domains\Shared\Domains\Authorization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use InvalidArgumentException;
use Throwable;

class PermissionService
{
    private PermissionRepository $repository;

    private PermissionException $exception;

    public function __construct()
    {
        $this->repository = new PermissionRepository(new Permission());
        $this->exception = new PermissionException();
    }

    /**
     * @throws PermissionException
     */
    public function create(array $data): ?Permission
    {
        try {
            $created = $this->repository->create($data);

            if (! $created instanceof Permission) {
                throw $this->exception::createFailed();
            }

            return $created;

        } catch (InvalidArgumentException $e) {
            throw $this->exception::invalidData($e->getMessage());
        } catch (ModelNotFoundException $e) {
            throw $this->exception::createFailed($e->getMessage());
        } catch (QueryException $e) {
            if (23505 === $e->errorInfo[1]) {
                throw $this->exception::duplicate(name: $data['name'] ?? 'unknown');
            }
            throw $this->exception::unexpected($e->getMessage());
        } catch (Throwable $e) {
            throw $this->exception::unexpected($e->getMessage());
        }
    }

    /**
     * @throws PermissionException
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
}
