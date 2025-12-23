<?php

namespace App\Policies;

use App\Models\RequestDetail;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RequestDetailPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     * (Generalmente no se accede individualmente a un detalle)
     */
    public function view(User $user, RequestDetail $requestDetail): bool
    {
        // Acceso a través de la solicitud padre
        return $user->can('view', $requestDetail->request);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Controlado por la solicitud padre (RequestPolicy)
        return $user->role === 'usuario';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RequestDetail $requestDetail): bool
    {
        // Solo el usuario dueño de la solicitud padre puede actualizar detalles
        // y solo si la solicitud está en draft o pending_decision
        if ($user->role === 'usuario' && $user->dependency->id === $requestDetail->request->dependency_id) {
            return in_array($requestDetail->request->status, ['draft', 'pending_decision']);
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RequestDetail $requestDetail): bool
    {
        // Solo el usuario dueño de la solicitud padre puede eliminar detalles
        // y solo si la solicitud está en draft o pending_decision
        if ($user->role === 'usuario' && $user->dependency->id === $requestDetail->request->dependency_id) {
            return in_array($requestDetail->request->status, ['draft', 'pending_decision']);
        }
        return false;
    }
}