<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'thumbnails',
        'product_id'
    ];

    protected $appends = ['full_thumbnail_link'];

    public function Product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getFullThumbnailLinkAttribute()
    {
        $image_name = str_replace('"', '', $this->attributes['thumbnails']);

        return "https://api.ztrademm.com/storage/product_image/" . $image_name;
    }
}
