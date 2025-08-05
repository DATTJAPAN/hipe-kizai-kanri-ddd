<?php

declare(strict_types=1);

namespace App\Domains\Shared\Authorization\Contract;

interface HasPermissionContract
{
    public function getDefinedPermissionList(): array;
}
