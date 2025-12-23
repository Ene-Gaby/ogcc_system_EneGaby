<?php

namespace App\Policies;

use App\Models\Dependency;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DependencyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'administrador' || $user->role === 'analista' || $user->role === 'supervisor';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Dependency $dependency): bool
    {
        // Admin, Analista y Supervisor pueden ver cualquier dependencia
        if (in_array($user->role, ['administrador', 'analista', 'supervisor'])) {
            return true;
        }
        // La dependencia puede ver solo su propia información (si se accede directamente)
        // Esto es más común si se accede a través de $user->dependency
        return $user->role === 'usuario' && $user->dependency->id === $dependency->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'administrador';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Dependency $dependency): bool
    {
        return $user->role === 'administrador';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Dependency $dependency): bool
    {
        return $user->role === 'administrador';
    }
}