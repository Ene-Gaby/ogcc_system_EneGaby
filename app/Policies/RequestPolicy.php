<?php

namespace App\Policies;

use App\Models\Request;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Analista, Supervisor y Admin pueden ver todas las solicitudes
        return in_array($user->role, ['administrador', 'analista', 'supervisor']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Request $request): bool
    {
        // Analista, Supervisor y Admin pueden ver cualquier solicitud
        if (in_array($user->role, ['administrador', 'analista', 'supervisor'])) {
            return true;
        }
        // El usuario puede ver solo sus propias solicitudes
        return $user->role === 'usuario' && $user->dependency->id === $request->dependency_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Solo los usuarios pueden crear solicitudes
        return $user->role === 'usuario';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Request $request): bool
    {
        // Solo el usuario dueño puede actualizar su solicitud si está en estado draft o pending_decision
        if ($user->role === 'usuario' && $user->dependency->id === $request->dependency_id) {
            return in_array($request->status, ['draft', 'pending_decision']);
        }
        // Analista, Supervisor y Admin pueden actualizar (validar, consolidar, etc.)
        if (in_array($user->role, ['administrador', 'analista', 'supervisor'])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Request $request): bool
    {
        // Solo el usuario dueño puede eliminar su solicitud si está en estado draft o pending_decision
        if ($user->role === 'usuario' && $user->dependency->id === $request->dependency_id) {
            return in_array($request->status, ['draft', 'pending_decision']);
        }
        // Admin puede eliminar (en casos especiales)
        if ($user->role === 'administrador') {
            return true;
        }
        return false;
    }

    // Métodos específicos para acciones del flujo
    public function participate(User $user, Request $request): bool
    {
        // Solo el usuario dueño puede participar si la solicitud es suya y está en pending_decision
        return $user->role === 'usuario' && $user->dependency->id === $request->dependency_id && $request->status === 'pending_decision';
    }

    public function notParticipate(User $user, Request $request): bool
    {
        // Solo el usuario dueño puede decidir no participar si la solicitud es suya y está en pending_decision
        return $user->role === 'usuario' && $user->dependency->id === $request->dependency_id && $request->status === 'pending_decision';
    }

    public function submit(User $user, Request $request): bool
    {
        // Solo el usuario dueño puede enviar la solicitud si es suya y está en draft o pending_decision
        return $user->role === 'usuario' && $user->dependency->id === $request->dependency_id && in_array($request->status, ['draft', 'pending_decision']);
    }
}