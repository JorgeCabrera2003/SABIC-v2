<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    use HasFactory;

    protected $fillable = [
        'document',
        'name',
        'last_name',
        'phone_number',
        'email',
        'id_nominal_location',
        'id_position',
        'photo_dir',
        'status', // Agregar este campo
    ];

    public $timestamps = false;

    // Relación con Asistencias
    public function asistencias()
    {
        return $this->hasMany(Asistencias::class, 'id_personal');
    }

    // Scope para empleados activos
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Método para verificar si puede registrar asistencia
    public function canRegisterAttendance(): bool
    {
        $allowedStatuses = ['active', 'authorized'];
        return in_array($this->status, $allowedStatuses);
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'id_position');
    }

    public function nominalLocation()
    {
        return $this->belongsTo(NominalLocation::class, 'id_nominal_location');
    }
}
