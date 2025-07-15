<?php

declare(strict_types=1);

namespace App\Domains\Shared\Repository;

use App\Domains\Shared\Contracts\BaseRepositoryContract;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;
use Throwable;

/**
 * @template TModel of Model
 *
 * @implements BaseRepositoryContract<TModel>
 */
abstract class BaseRepository implements BaseRepositoryContract
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection<int, TModel>
     */
    public function getList(
        ?array $filters = [],
        ?array $sorts = [],
        ?int $limit = 10,
        ?int $offset = 0,
        ?array $relations = []
    ): Collection {
        $query = $this->model->newQuery();

        if (! empty($relations)) {
            $query->with($relations);
        }

        foreach ($filters as $field => $value) {
            $query->where($field, $value);
        }

        foreach ($sorts as $field => $direction) {
            $query->orderBy($field, $direction);
        }

        return $query->limit($limit)->offset($offset)->get();
    }

    public function getById(int $id, ?array $relations = []): ?Model
    {
        return $this->model->with($relations)->find($id);
    }

    /**
     * @throws ModelNotFoundException
     * @throws InvalidArgumentException
     */
    public function create(array $data, array $options = []): Model
    {
        if (empty($data)) {
            throw new InvalidArgumentException('Data array cannot be empty.');
        }

        try {
            return DB::transaction(function () use ($data, $options) {
                /** @var Model $model */
                $model = $this->model->create($data);

                if (! empty($options['with'])) {
                    $model->load($options['with']);
                }

                return $model;
            });
        } catch (Throwable $e) {
            throw new ModelNotFoundException('Failed to create model: '.$e->getMessage());
        }
    }

    /**
     * @throws ModelNotFoundException|Throwable
     */
    public function update(int $id, array $data): Model
    {
        return DB::transaction(function () use ($id, $data) {
            /** @var Model|null $model */
            $model = $this->model->find($id);

            if (! $model) {
                throw new ModelNotFoundException("Model with ID {$id} not found.");
            }

            $model->update($data);

            return $model->fresh();
        });
    }

    /**
     * @throws Throwable
     */
    public function updateModel(Model $model, array $data, ...$options): Model
    {
        return DB::transaction(static function () use ($model, $data, $options) {
            $model->update($data);

            return $model->fresh($options);
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            /** @var Model|null $model */
            $model = $this->model->find($id);

            if (! $model) {
                throw new ModelNotFoundException("Model with ID {$id} not found.");
            }

            return (bool) $model->delete();
        });
    }
}
