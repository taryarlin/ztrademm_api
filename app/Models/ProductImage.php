<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;
    protected $fillable =[
        'thumbnails',
        'product_id'
    ];
    public function Product(){
        return $this->belongsTo(Product::class,'product_id');
    }
}
