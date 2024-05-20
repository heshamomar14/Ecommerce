<?php

namespace App\Models;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use App\Models\SubCategory;

class Brands extends Model
{
    use HasFactory;
    protected $fillable=['name','slug',];



}
