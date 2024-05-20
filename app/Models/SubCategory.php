<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class SubCategory extends Model
{
    use HasFactory;
    protected $fillable=['name','slug','status','category_id'];



    public function Categories()
    {
        return $this->hasOne(Category::class,'id','category_id');
    } public function Category()
    {
        return $this->belongsTo(Category::class);
    }
}
