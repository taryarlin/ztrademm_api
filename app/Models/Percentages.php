<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Percentages extends Model
{
    use HasFactory;

    protected $fillable = [
        'percentage'
    ];
}
