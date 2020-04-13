<?php

namespace App\Providers;

use App\Franchise;
use App\Lead;
use App\Policies\FranchisePolicy;
use App\Policies\LeadPolicy;
use App\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        Franchise::class => FranchisePolicy::class,
        Lead::class => LeadPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('user_access', function($user){
            return $user->isHeadOffice();
        });

        Gate::define('attach_franchise', function($user){
            return $user->isHeadOffice();
        });
    }
}
