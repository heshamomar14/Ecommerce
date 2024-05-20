<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 use App\Models\ProductImage;

class Product extends Model
{
    use HasFactory;

    protected $fillable =
        [
            'title'
            , 'slug'
            , 'category_id'
            , 'sub_category_id'
            , 'brand_id'
            , 'description'
            , 'price'
            , 'compare_price'
            , 'is_featured'
            , 'sku'
            , 'barcode'
            , 'qty',
            'track_qty'
            , 'status'
        ];


    public function product_images()
    {
        return $this->hasMany(ProductImage::class);
    }
    public function product_ratings()
    {
        return $this->hasMany(ProductRating::class)->where('status',1);
    }

}
