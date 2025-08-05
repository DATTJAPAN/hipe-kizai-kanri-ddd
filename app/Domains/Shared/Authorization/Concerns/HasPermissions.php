<?php

declare(strict_types=1);

namespace App\Domains\Shared\Authorization\Concerns;

trait HasPermissions
{
    private const BASIC_FORMAT = '%s_%s';

    private const DOMAIN_FORMAT = '%s::%s_%s';

    private const SCOPE_DOMAIN_FORMAT = '%s::%s::%s_%s';

    public function makeBasicPermission(string $action, $resource): string
    {
        return sprintf(self::BASIC_FORMAT, $action, $resource);
    }

    public function makeDomainPermission(string|self|null $domain, string $action, $resource): string
    {
        $useDomain = match (true) {
            null === $domain, $domain instanceof self => $this->getDomainFromClass(),
            default => $domain
        };

        return sprintf(self::DOMAIN_FORMAT, $useDomain, $action, $resource);
    }

    /**
     * @param  string|HasPermissions|null  $domain
     */
    public function makeMultiDomainPermission(string|self|null $domain, $resource, array $actions): array
    {
        $permissions = [];
        foreach ($actions as $action) {
            $permissions[] = $this->makeDomainPermission($domain, $action, $resource);
        }

        return array_unique($permissions);
    }

    public function makeScopeDomainPermission(string|self|null $domain, string $scope, string $action, $resource): string
    {
        $useDomain = match (true) {
            null === $domain, $domain instanceof self => $this->getDomainFromClass(),
            default => $domain
        };

        return sprintf(self::SCOPE_DOMAIN_FORMAT, $useDomain, $scope, $action, $resource);
    }

    /**
     * Get the unique list of permissions base on the defined list
     *
     * @return array<string> unique
     */
    public function getDefinedPermissionList(): array
    {
        return array_unique($this->definePermissionList());
    }

    /**
     * Register a list of permissions
     *
     * @return array<string>
     */
    protected function definePermissionList(): array
    {
        return []; // TODO: add if needed
    }

    private function getDomainFromClass(): string
    {
        return str(class_basename($this))
            ->lower()
            ->toString();
    }
}
