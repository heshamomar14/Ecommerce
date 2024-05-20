<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//use App\Models\SubCategory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'status'];


    public function subcategories()
    {
        return $this->hasMany(SubCategory::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

//    public function image()
//    {
//        return $this->morphOne(Media::class ,'mediaable');
//    }
}
