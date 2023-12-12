<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishList extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'user_id'
    ]; 

    public function User(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function Product(){
        return $this->belongsTo(Product::class,'product_id');
    }
}
