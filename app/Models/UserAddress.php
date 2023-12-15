<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','street','city','state','postal_code','country','phone'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
