<?php

declare(strict_types=1);

namespace App\Domains\Shared\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 */
interface BaseRepositoryContract
{
    public function getList(
        ?array $filters = [],
        ?array $sorts = [],
        ?int $limit = 10,
        ?int $offset = 0,
        ?array $relations = []
    ): Collection;

    public function getById(int $id, ?array $relations = []): ?Model;

    public function create(array $data): Model;

    public function update(int $id, array $data): Model;

    public function updateModel(Model $model, array $data): Model;

    public function delete(int $id): bool;
}
