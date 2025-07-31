<?php

declare(strict_types=1);

namespace App\Domains\Shared\Authorization\Concerns;

trait HasRoles
{
    public function assignRoles(string ...$roles): void {}
}
