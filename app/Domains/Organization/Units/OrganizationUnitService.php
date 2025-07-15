<?php

declare(strict_types=1);

namespace App\Domains\Organization\Units;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class OrganizationUnitService
{
    private OrganizationUnitRepository $repository;

    private OrganizationUnitException $exception;

    private OrganizationUnit $modelInstance;

    public function __construct()
    {
        $this->modelInstance = new OrganizationUnit();
        $this->repository = new OrganizationUnitRepository(model: $this->modelInstance);
        $this->exception = new OrganizationUnitException();
    }


    /**
     * @throws OrganizationUnitBuilderException
     * @throws OrganizationUnitException
     */
    public function createUsingBuilder(OrganizationUnitBuilder $builder): OrganizationUnit
    {
        return $this->create($builder->build()->toArray());
    }

    /**
     * @throws OrganizationUnitException
     */
    public function create(array $data): OrganizationUnit
    {
        $data = $this->modelInstance->filterFillableAttributes($data);

        try {
            $created = $this->repository->create($data);

            if (!$created instanceof OrganizationUnit) {
                throw $this->exception::createFailed();
            }

            return $created;

        } catch (\InvalidArgumentException $e) {
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
        } catch (\Throwable $e) {
            throw $this->exception::unexpected($e->getMessage());
        }
    }
}
