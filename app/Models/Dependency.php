<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dependency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'responsible',
        'organizational_structure',
        'user_id',
    ];

    // Relación: Una Dependencia pertenece a un Usuario (el administrador)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: Una Dependencia tiene muchas Solicitudes (Request)
    public function requests()
    {
        return $this->hasMany(Request::class);
    }
}