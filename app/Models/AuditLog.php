<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'auditable_id',
        'auditable_type',
        'user_id',
        'action',
        'old_values',
        'new_values',
        'action_time',
    ];

    // Relación: Un Registro de Auditoría pertenece a un Usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: Un Registro de Auditoría puede ser de cualquier modelo (polimórfica)
    public function auditable()
    {
        return $this->morphTo();
    }
}