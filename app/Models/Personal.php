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
        'nominal_location',
        'position',
        'photo_dir',
    ];
    
    public $timestamps = false;
}