<?php

namespace App\Providers;

use App\Respositories\FranchiseRepository;
use App\Respositories\FranchiseRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->app->bind(FranchiseRepositoryInterface::class, FranchiseRepository::class);
        
    }
}
