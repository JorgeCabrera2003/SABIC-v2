<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
use Promethys\FilamentRevive\Concerns\Recyclable;

class NominalLocation extends Model
{
    use HasFactory, SoftDeletes, Recyclable;

    protected $table = 'nominal_location';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'floor',
    ];
}
