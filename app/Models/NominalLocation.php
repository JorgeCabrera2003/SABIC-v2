<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NominalLocation extends Model
{
    use HasFactory;

    protected $table = 'nominal_location';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'floor',
    ];
}
