<?php

namespace App\Policies;

use App\Models\AcquisitionProcess;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AcquisitionProcessPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Tanto Analista, Supervisor como Admin pueden ver los procesos
        // Los usuarios también pueden ver procesos abiertos (esto se manejará en el controlador)
        return in_array($user->role, ['administrador', 'analista', 'supervisor', 'usuario']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AcquisitionProcess $acquisitionProcess): bool
    {
        // Admin, Analista y Supervisor pueden ver cualquier proceso
        if (in_array($user->role, ['administrador', 'analista', 'supervisor'])) {
            return true;
        }
        // El usuario puede ver un proceso si está abierto
        return $user->role === 'usuario' && $acquisitionProcess->status === 'open';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'administrador' || $user->role === 'analista';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AcquisitionProcess $acquisitionProcess): bool
    {
        // Solo Admin o Analista pueden actualizar un proceso
        return in_array($user->role, ['administrador', 'analista']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AcquisitionProcess $acquisitionProcess): bool
    {
        // Solo Admin o Analista pueden eliminar un proceso (si no tiene solicitudes asociadas)
        return in_array($user->role, ['administrador', 'analista']);
    }
}