<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

// Importar modelos
use App\Models\User;
use App\Models\Dependency;
use App\Models\AcquisitionProcess;
use App\Models\Rubro;
use App\Models\Request;
use App\Models\RequestDetail;
use App\Models\AuditLog;

// Importar policies
use App\Policies\UserPolicy;
use App\Policies\DependencyPolicy;
use App\Policies\AcquisitionProcessPolicy;
use App\Policies\RubroPolicy;
use App\Policies\RequestPolicy;
use App\Policies\RequestDetailPolicy;
use App\Policies\AuditLogPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // ...
        User::class => UserPolicy::class,
        Dependency::class => DependencyPolicy::class,
        AcquisitionProcess::class => AcquisitionProcessPolicy::class,
        Rubro::class => RubroPolicy::class,
        Request::class => RequestPolicy::class,
        RequestDetail::class => RequestDetailPolicy::class,
        AuditLog::class => AuditLogPolicy::class,
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