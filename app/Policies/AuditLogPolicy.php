<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuditLogPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Generalmente solo Admin, Analista o Supervisor pueden ver auditoría
        return in_array($user->role, ['administrador', 'analista', 'supervisor']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AuditLog $auditLog): bool
    {
        // Similar a viewAny, solo roles autorizados
        return in_array($user->role, ['administrador', 'analista', 'supervisor']);
    }
}