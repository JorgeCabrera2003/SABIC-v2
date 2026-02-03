<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    use HasFactory;
    protected $fillable = [
        'cedula',
        'nombre',
        'apellido',
        'telefono',
        'email',
        'ubicacion_nominal',
        'cargo',
        'foto_dir',
        'curriculo_dir',
    ];
}