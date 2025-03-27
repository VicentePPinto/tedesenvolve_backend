<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Task;
use App\Models\User;
use App\Policies\CompanyPolicy;
use App\Policies\TaskPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        Task::class => TaskPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Registrar a política para o modelo User
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(Company::class, CompanyPolicy::class);
    }
}
