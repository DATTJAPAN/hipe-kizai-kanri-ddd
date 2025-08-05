<?php

declare(strict_types=1);

namespace App\Domains\Organization\Users;

trait HasOrganizationUserAsCreator
{
    protected string $creatorForeignKey = 'creator_org_user_id';

    public static function bootHasOrganizationUserAsCreator(): void
    {
        static::creating(static function ($model) {
            $auth = auth()->guard(name: activeGuard());
            $authUser = $auth->check() ? $auth->user() : null;

            // Auto inject creator attr to model
            if ($authUser instanceof OrganizationUser) {
                $model->{$model->creatorForeignKey} ??= $authUser->id ?? $authUser->getKey() ?? null;
            }
        });
    }
}
