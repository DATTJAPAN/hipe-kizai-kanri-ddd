<?php

declare(strict_types=1);

namespace App\Domains\Organization\Users;

class OrganizationUserService
{
    private OrganizationUserRepository $repository;

    public function __construct()
    {
        $this->repository = new OrganizationUserRepository(new OrganizationUser());
    }
}
