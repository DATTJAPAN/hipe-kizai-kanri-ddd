<?php

declare(strict_types=1);

namespace App\Domains\Shared\Authorization;

use App\Domains\Shared\Repository\BaseRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class PermissionRepository extends BaseRepository
{
    public function __construct(Permission $model)
    {
        parent::__construct(model: $model);
    }

    public function getCurrentPermissionNames(): Collection
    {
        return DB::table($this->model->getTable())
            ->select('name')
            ->pluck('name');
    }

    /**
     * @throws Throwable
     */
    public function syncPermissions(array $forRemoval, array $forAddition)
    {
        return DB::transaction(function () use ($forRemoval, $forAddition) {
            if (! empty($forRemoval)) {
                DB::table($this->model->getTable())
                    ->whereIn('name', $forRemoval)
                    ->delete();
            }

            if (! empty($forAddition)) {
                $permissions = array_map(function ($name) {
                    return [
                        'prefixed_id' => $this->model->getGeneratedPrefixedId(),
                        'name' => $name,
                        'display_name' => $name,
                        'is_app_default' => true, // indicate as permanent
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }, $forAddition);

                DB::table($this->model->getTable())
                    ->insert($permissions);
            }
        });

    }
}
