<?php

declare(strict_types=1);

namespace App\Domains\Organization\Users;

trait HasOrganizationCreator
{
    protected string $creatorForeignKey = 'creator_org_user_id';

    public static function bootHasOrganizationCreator(): void
    {
        static::creating(function ($model) {
            $auth = auth()->guard(name: activeGuard());
            $authUser = $auth->check() ? $auth->user() : null;

            // Model will inherit the 'org_id' from the logged User
            if ($authUser instanceof OrganizationUser) {
                $model->{$this->creatorForeignKey} ??= $authUser->getKey();
            }
        });
    }
}
