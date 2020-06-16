<?php

namespace App\Providers;

use App\Repositories\DocumentRepository;
use App\Repositories\FranchiseRepository;
use App\Repositories\Interfaces\DocumentRepositoryInterface;
use App\Repositories\Interfaces\FranchiseRepositoryInterface;
use App\Repositories\Interfaces\LeadRepositoryInterface;
use App\Repositories\Interfaces\PostcodeRepositoryInterface;
use App\Repositories\Interfaces\SalesContactRepositoryInterface;
use App\Repositories\Interfaces\SalesStafRepositoryInterface;
use App\Repositories\Interfaces\TradeStaffRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\LeadRepository;
use App\Repositories\PostcodeRepository;
use App\Repositories\SalesContactRepository;
use App\Repositories\SalesStaffRepository;
use App\Repositories\TradeStaffRepository;
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
        $this->app->bind(SalesContactRepositoryInterface::class, SalesContactRepository::class);
        $this->app->bind(DocumentRepositoryInterface::class, DocumentRepository::class);
        $this->app->bind(SalesStafRepositoryInterface::class, SalesStaffRepository::class);
        $this->app->bind(PostcodeRepositoryInterface::class, PostcodeRepository::class);
        $this->app->bind(TradeStaffRepositoryInterface::class, TradeStaffRepository::class);
    }
}
