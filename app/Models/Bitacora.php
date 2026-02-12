<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Promethys\FilamentRevive\Concerns\Recyclable;

class Bitacora extends Model
{
    use HasFactory, SoftDeletes, Recyclable;

    protected $table = 'bitacora';

    protected $fillable = [
        'accion',
        'descripcion',
        'tabla_afectada',
        'registro_id',
        'valores_anteriores',
        'valores_nuevos',
        'bd_user',
        'ip_address',
        'user_agent',
        'user_id'
    ];

    protected $casts = [
        'valores_anteriores' => 'array',
        'valores_nuevos' => 'array',
        'fecha_hora' => 'datetime'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
