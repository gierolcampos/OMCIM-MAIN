<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\Announcement;
use App\Models\User;
use App\Policies\EventPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Event::class => EventPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define gates for permissions
        Gate::define('manage-events', function (User $user) {
            return $user->canManageEvents();
        });

        Gate::define('manage-announcements', function (User $user) {
            return $user->canManageAnnouncements();
        });

        Gate::define('manage-payments', function (User $user) {
            return $user->canManagePayments();
        });

        Gate::define('manage-reports', function (User $user) {
            return $user->canManageReports();
        });

        Gate::define('manage-members', function (User $user) {
            return $user->canManageMembers();
        });

        // Define gates for roles
        Gate::define('superadmin', function (User $user) {
            return $user->isSuperadmin();
        });

        Gate::define('officer', function (User $user) {
            return $user->isOfficer();
        });

        Gate::define('admin', function (User $user) {
            return $user->isAdmin();
        });

        // Define gates for committee management
        Gate::define('manage-committees', function (User $user) {
            return $user->isSuperadmin();
        });

        // Define gates for user management
        Gate::define('manage-users', function (User $user) {
            return $user->isSuperadmin();
        });
    }
}
