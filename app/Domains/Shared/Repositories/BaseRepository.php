<?php

declare(strict_types=1);

namespace App\Domains\Shared\Repositories;

use Closure;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\PrefixedIds\Exceptions\NoPrefixedModelFound;

/**
 * @template TModel of Model
 *
 * @implements BaseRepositoryInterface<TModel>
 */
abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    protected bool $withTrashed = false;

    protected bool $onlyTrashed = false;

    protected array $with = [];

    protected bool $deletePermanent = false;

    protected ?Closure $applyAlwaysQueryCallback = null;

    protected ?Closure $applyOnceQueryCallback = null;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function tapQueryAlways(Closure $cb): self
    {
        $this->applyAlwaysQueryCallback = $cb;

        return $this;
    }

    public function tapQueryOnce(Closure $cb): self
    {
        $this->applyOnceQueryCallback = $cb;

        return $this;
    }

    public function showTrashedData(): self
    {
        $this->withTrashed = true;
        // disable only trashed
        $this->onlyTrashed = false;

        return $this;
    }

    public function showOnlyTrashedData(): self
    {
        $this->onlyTrashed = true;
        // disable with trashed
        $this->withTrashed = false;

        return $this;
    }

    public function withRelations(array $relations): self
    {
        $this->with = $relations;

        return $this;
    }

    public function deletePermanently(): self
    {
        $this->deletePermanent = true;

        return $this;
    }

    public function dataTable(): EloquentCollection|Collection|array
    {
        return $this->prepareQuery()
            ->get();
    }

    public function findById(int $identifier, ?array $relations = []): ?Model
    {
        return $this->prepareQuery()
            ->with($this->with ?? $relations)
            ->findOrFail($identifier);
    }

    /**
     * @throws NoPrefixedModelFound
     */
    public function findByPrefixedId(string $identifier, ?array $relations = []): ?Model
    {
        $attributeName = config('prefixed-ids.prefixed_id_attribute_name');
        $model = $this->prepareQuery()
            ->where($attributeName, $identifier)
            ->with($this->with ?? $relations)
            ->first();

        if (is_null($model)) {
            throw NoPrefixedModelFound::make(prefixedId: $identifier);
        }

        return $model;
    }

    // ---> Create

    public function create(array $data): Model
    {
        $this->ensureNoEmptyData(data: $data);

        return DB::transaction(function () use ($data) {
            return $this->model->create(attributes: $data);
        });
    }

    // ---> Update
    public function updateById(int $identifier, array $data): Model
    {
        return DB::transaction(function () use ($identifier, $data) {
            $m = $this->findById(identifier: $identifier);
            $m?->update(attributes: $data);

            return $m?->fresh(with: $this->with);
        });
    }

    public function updateByPrefixedId(string $identifier, array $data): Model
    {
        return DB::transaction(function () use ($identifier, $data) {
            $m = $this->findByPrefixedId(identifier: $identifier);
            $m?->update(attributes: $data);

            return $m?->fresh(with: $this->with);
        });
    }

    public function updateByIdOrPrefixedId(int|string $identifier, array $data): Model
    {
        return is_int($identifier)
            ? $this->updateById(identifier: $identifier, data: $data)
            : $this->updateByPrefixedId(identifier: $identifier, data: $data);
    }

    public function updateModel(Model $model, array $data): Model
    {
        return DB::transaction(function () use ($model, $data) {
            $model->update(attributes: $data);

            return $model->fresh(with: $this->with);
        });
    }

    // ---> Delete
    public function deleteById(int $identifier): bool
    {
        return DB::transaction(function () use ($identifier) {
            $m = $this->findById(identifier: $identifier);

            return $this->executeSoftOrForceDeletion(model: $m);
        });
    }

    public function deleteByPrefixedId(string $identifier): bool
    {
        return DB::transaction(function () use ($identifier) {
            $m = $this->findByPrefixedId(identifier: $identifier);

            return $this->executeSoftOrForceDeletion(model: $m);
        });
    }

    public function deleteByIdOrPrefixedId(int|string $identifier): bool
    {
        return is_int($identifier)
            ? $this->deleteById(identifier: $identifier)
            : $this->deleteByPrefixedId(identifier: $identifier);
    }

    public function deleteModel(Model $model): bool
    {
        return DB::transaction(function () use ($model) {
            return $this->executeSoftOrForceDeletion(model: $model);
        });
    }

    public function restoreById(int $identifier): bool
    {
        return DB::transaction(function () use ($identifier) {
            return $this->findById(identifier: $identifier)?->restore();
        });
    }

    public function restoreByPrefixedId(string $identifier): bool
    {
        return DB::transaction(function () use ($identifier) {
            return $this->findByPrefixedId(identifier: $identifier)?->restore();
        });
    }

    public function restoreByIdOrPrefixedId(int|string $identifier): bool
    {
        return is_int(value: $identifier)
            ? $this->restoreById(identifier: $identifier)
            : $this->restoreByPrefixedId(identifier: $identifier);
    }

    public function restoreModel(Model $model): bool
    {
        return DB::transaction(function () use ($model) {
            return $model->restore();
        });
    }

    // ---> Helpers

    private function ensureNoEmptyData(array $data): void
    {
        if (empty($data)) {
            throw BaseRepositoryException::emptyData();
        }
    }

    private function prepareQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = $this->model->newQuery();

        // Check if the model uses SoftDeletes
        if (method_exists(object_or_class: $this->model, method: 'isUsingSoftDeletes') && $this->model->isUsingSoftDeletes()) {
            if ($this->withTrashed) {
                $query->withTrashed();
            }

            if ($this->onlyTrashed) {
                $query->onlyTrashed();
            }
        }

        if ($this->applyAlwaysQueryCallback) {
            $query = call_user_func($this->applyAlwaysQueryCallback, $query);
        }

        if ($this->applyOnceQueryCallback) {
            $query = call_user_func($this->applyOnceQueryCallback, $query);
            $this->applyOnceQueryCallback = null;
        }

        return $query;
    }

    private function executeSoftOrForceDeletion(?Model $model): bool
    {
        if ($this->deletePermanent) {
            return (bool) $model?->forceDelete();
        }

        return (bool) $model?->delete();
    }
}
