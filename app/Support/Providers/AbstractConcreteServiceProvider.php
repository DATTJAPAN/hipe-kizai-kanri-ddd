<?php

declare(strict_types=1);

namespace App\Support\Providers;

use App\Core\Authentication\AuthenticationContract;
use App\Core\Authentication\AuthenticationService;
use App\Domains\Shared\Contracts\BaseRepositoryContract;
use App\Domains\Shared\Repository\BaseRepository;
use App\Domains\System\Organizations\OrganizationRepository;
use App\Domains\System\Organizations\OrganizationService;
use Illuminate\Support\ServiceProvider;

class AbstractConcreteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //        $this->app->bind(
        //            abstract: AuthenticationContract::class,
        //            concrete: AuthenticationService::class
        //        );
        //
        //        $this->app->bind(
        //            abstract: BaseRepositoryContract::class,
        //            concrete: BaseRepository::class
        //        );
        //
        //        $this->app->bind(
        //            abstract: BaseRepository::class,
        //            concrete: OrganizationRepository::class
        //        );
        //
        //        $this->app->bind(abstract: OrganizationService::class);
    }
}
