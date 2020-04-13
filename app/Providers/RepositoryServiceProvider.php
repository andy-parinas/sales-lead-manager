<?php

namespace App\Providers;

use App\Repositories\FranchiseRepository;
use App\Repositories\Interfaces\FranchiseRepositoryInterface;
use App\Repositories\Interfaces\LeadRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\LeadRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        $this->app->bind(FranchiseRepositoryInterface::class, FranchiseRepository::class);
        $this->app->bind(LeadRepositoryInterface::class, LeadRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }
}
