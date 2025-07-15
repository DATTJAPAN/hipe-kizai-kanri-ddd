<?php

declare(strict_types=1);

namespace App\Domains\Shared\Domains\Authorization;

use App\Domains\Shared\Domains\Organizations\Organization;

class RoleBuilder
{
    private string $name;

    private string $alias;

    private int $hierarchy = 99;

    private string $guardName = 'web';

    private ?Organization $organization = null;

    public function __construct() {}

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function setGuardName(string $guardName): self
    {
        $this->guardName = $guardName;

        return $this;
    }

    public function setHierarchy(int $hierarchy): self
    {
        $this->hierarchy = $hierarchy;

        return $this;
    }

    /**
     * @throws RoleBuilderException
     */
    public function setForOrganizationOnly(
        Organization $organization,
    ): self {
        if ($organization->isNew()) {
            throw RoleBuilderException::unexpected('Organization must be existing.');
        }

        $this->organization = $organization;

        return $this;
    }

    /**
     * @return Role instance
     */
    public function build(): Role
    {
        $role = new Role([
            'name' => $this->name,
            'display_name' => $this->alias,
            'guard_name' => $this->guardName,
            'hierarchy' => $this->hierarchy,
        ]);

        if (null !== $this->organization) {
            $prefixed = $this->organization->prefixed_id;
            $role->name = "{$prefixed}::{$role->name}";
            $role->organization_id = $this->organization->id;
            $role->organization_type = $this->organization::class;
        }

        return $role;
    }
}
