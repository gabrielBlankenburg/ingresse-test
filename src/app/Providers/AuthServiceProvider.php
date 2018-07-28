<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        Gate::define('create-user', function($user) {
            return $user->admin == 1;
        });
        Gate::define('update-user', function($user, $id) {
            return $user->admin == 1 || $user->id == $id;
        });
        Gate::define('delete-user', function($user, $id) {
            return $user->admin == 1 || $user->id == $id;
        });
    }
}
