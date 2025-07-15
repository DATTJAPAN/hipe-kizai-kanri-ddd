<?php

declare(strict_types=1);

namespace App\Domains\Organization\Users;

use App\Domains\Shared\Domains\Organizations\Organization;

class OrganizationUserBuilder
{
    private string $username;

    private string $email;

    private string $password;

    private Organization $parentOrganization;

    private ?OrganizationUser $creator = null;

    private function __construct()
    {
        if (
            auth(guard: activeGuard())->check() &&
            auth(guard: activeGuard())->user() instanceof OrganizationUser) {
            $this->creator = auth(guard: activeGuard())->user();
        }
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function setParentOrganization(Organization $parentOrganization): self
    {
        $this->parentOrganization = $parentOrganization;

        return $this;
    }

    public function build(): OrganizationUser
    {
        $instance = new OrganizationUser(attributes: [
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
        ]);

        $instance->creator_org_user_id = $this->creator->id;
        $instance->org_id = $this->parentOrganization->id;

        return $instance;
    }
}
