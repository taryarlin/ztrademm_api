<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
        'number',
        'unique_id'
    ]; 
    public function SubCategory(){
        return $this->hasMany(SubCategory::class,'category_id');
    }
    public function Product(){
        return $this->hasMany(Product::class,'category_id');
    }
}
