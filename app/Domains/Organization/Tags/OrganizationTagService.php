<?php

declare(strict_types=1);

namespace App\Domains\Organization\Tags;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use InvalidArgumentException;
use Throwable;

class OrganizationTagService
{
    private OrganizationTagRepository $repository;

    private OrganizationTagException $exception;

    private OrganizationTag $modelInstance;

    public function __construct()
    {
        $this->modelInstance = new OrganizationTag();
        $this->repository = new OrganizationTagRepository(model: $this->modelInstance);
        $this->exception = new OrganizationTagException();
    }

    /**
     * @throws OrganizationTagException
     */
    public function create(array $data): OrganizationTag
    {
        $data = $this->modelInstance->filterFillableAttributes($data);

        try {
            $created = $this->repository->create($data);

            if (! $created instanceof OrganizationTag) {
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
                );
            }
            throw $this->exception::unexpected($e->getMessage());
        } catch (Throwable $e) {
            throw $this->exception::unexpected($e->getMessage());
        }
    }

    /**
     * @throws OrganizationTagException
     */
    public function update(int $id, array $data): OrganizationTag
    {
        try {
            if (empty($data)) {
                throw $this->exception::invalidData('Update data cannot be empty.');
            }

            $model = $this->repository->update($id, $data);

            if (! $model instanceof OrganizationTag) {
                throw $this->exception::updateFailed();
            }

            return $model;
        } catch (ModelNotFoundException) {
            throw $this->exception::notFound($id);
        } catch (InvalidArgumentException $e) {
            throw $this->exception::invalidData($e->getMessage());
        } catch (Throwable $e) {
            throw $this->exception::unexpected($e->getMessage());
        }
    }

    /**
     * @throws OrganizationTagException
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
