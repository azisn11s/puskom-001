<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Team::class => \App\Policies\TeamPolicy::class,
        \App\Models\Territory::class => \App\Policies\TerritoryPolicy::class,
        \App\Models\Company::class => \App\Policies\CompanyPolicy::class,
        \App\Models\Employee::class => \App\Policies\EmployeePolicy::class,
        \App\Models\Country::class => \App\Policies\CountryPolicy::class,
        \App\Models\Region::class => \App\Policies\RegionPolicy::class,
        \App\Models\Area::class => \App\Policies\AreaPolicy::class,
        \App\Models\Doctor::class => \App\Policies\DoctorPolicy::class,
        \App\Models\Brand::class => \App\Policies\BrandPolicy::class,
        \App\Models\Role::class => \App\Policies\RolePolicy::class,
        \App\Models\CallPlan\MonthlySchedule::class => \App\Policies\CallPlanPolicy::class,
        'App\Models\User' => 'App\Policies\UserPolicy',
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
