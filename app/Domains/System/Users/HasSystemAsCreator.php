<?php

declare(strict_types=1);

namespace App\Domains\System\Users;

trait HasSystemAsCreator
{
    protected string $creatorSysUserIdForeignKey = 'creator_sys_user_id';

    public static function bootHasSystemAsCreator(): void
    {

        static::creating(static function ($model) {
            $auth = auth()->guard(name: activeGuard());
            $authUser = $auth->check() ? $auth->user() : null;

            if ($authUser instanceof SystemUser) {
                $model->{$model->creatorSysUserIdForeignKey} ??= $authUser->getKey();
            }
        });
    }
}
