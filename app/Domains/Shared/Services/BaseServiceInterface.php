<?php

declare(strict_types=1);

namespace App\Domains\Shared\Services;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface BaseServiceInterface
{
    // GET DATA'S
    public function dataTable(): EloquentCollection|Collection|array;

    // GET
    public function findById(int $identifier, ?array $relations = []): ?Model;

    public function findByPrefixedId(string $identifier, ?array $relations = []): ?Model;

    public function create(array $data): Model;

    public function updateByIdOrPrefixedId(int|string $identifier, array $data): Model;
}
