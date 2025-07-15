<?php

declare(strict_types=1);

namespace App\Domains\Shared\Domains\Authorization\Contract;

interface HasRoleContract
{
    public function definedRoles(): array;
}
