<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Promethys\FilamentRevive\Concerns\Recyclable;

class Attendance extends Model
{
    use HasFactory, SoftDeletes, Recyclable;

    protected $table = 'attendance';

    protected $fillable = [
        'id_personal',
        'day',
        'hour',
        'observation',
        'record_type',
    ];

    // Relación con Personal
    public function personal()
    {
        return $this->belongsTo(Personal::class, 'id_personal');
    }

    public $timestamps = false;

    // Scope para registros de hoy
    public function scopeToday($query)
    {
        return $query->where('day', now()->toDateString());
    }

    // Método para verificar si ya registró hoy
    public static function hasRegisteredToday($personalId): bool
    {
        return self::where('id_personal', $personalId)
            ->where('day', now()->toDateString())
            ->exists();
    }
}