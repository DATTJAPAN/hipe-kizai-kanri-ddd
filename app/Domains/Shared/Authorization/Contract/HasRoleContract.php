<?php

declare(strict_types=1);

namespace App\Domains\Shared\Authorization\Contract;

interface HasRoleContract
{
    public function definedRoles(): array;
}
