<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title=fake()->unique()->name();
        $slug=Str::slug($title);
        $subcategories=[5,8];
        $subcategorykey=array_rand($subcategories);
        $brands=[1,2,3];
        $brandskey=array_rand($brands);
        return [
            'title'=>$title,
            'slug'=>$slug,
            'category_id'=>16,
            'sub_category_id'=>$subcategories[$subcategorykey],
            'brand_id'=>$brands[$brandskey],
            'price'=>rand(10,1000),
            'compare_price'=>rand(100,1000),
            'sku'=>rand(100,10000),
            'track_qty'=>'Yes',
            'qty'=>10,
            'is_featured'=>'Yes'
            ,'status'=>1
        ];
    }
}
