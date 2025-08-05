<?php

declare(strict_types=1);

namespace App\Domains\Shared\Repositories;

use Closure;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @template TModel of Model
 */
interface BaseRepositoryInterface
{
    public function tapQueryAlways(Closure $cb): self;

    public function tapQueryOnce(Closure $cb): self;

    public function showTrashedData(): self;

    public function showOnlyTrashedData(): self;

    public function withRelations(array $relations): self;

    public function deletePermanently(): self;

    // GET DATA'S
    public function dataTable(): EloquentCollection|Collection|array;

    // GET
    public function findById(int $identifier, ?array $relations = []): ?Model;

    public function findByPrefixedId(string $identifier, ?array $relations = []): ?Model;

    // CREATE
    public function create(array $data): Model;

    // UPDATE
    public function updateById(int $identifier, array $data): Model;

    public function updateByPrefixedId(string $identifier, array $data): Model;

    public function updateByIdOrPrefixedId(int|string $identifier, array $data): Model;

    public function updateModel(Model $model, array $data): Model;

    // DELETE
    public function deleteById(int $identifier): bool;

    public function deleteByPrefixedId(string $identifier): bool;

    public function deleteByIdOrPrefixedId(int|string $identifier): bool;

    public function deleteModel(Model $model): bool;

    // RESTORE
    public function restoreById(int $identifier): bool;

    public function restoreByPrefixedId(string $identifier): bool;

    public function restoreByIdOrPrefixedId(int|string $identifier): bool;

    public function restoreModel(Model $model): bool;
}
