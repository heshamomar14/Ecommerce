<?php

namespace App\Http\Repositories;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductRating;

class ProductsRepository extends Controller
{
    protected  $productModel;
    protected $categoryModel;
    protected   $brandModel;

    public function __construct(Category $categoryModel, Brands $brandModel,Product $productModel)
    {
        $this->categoryModel = $categoryModel;
        $this->brandModel = $brandModel;
        $this->productModel = $productModel;

    }


    public function latest()
    {
        return $this->productModel->latest('id');

    }

    public function withProductImages()
    {
        return $this->productModel->with('product_images')->get();
    }

    public function searchByTitle($keyword)
    {
        return $this->productModel->where('title', 'like', '%' . $keyword . '%')->paginate(10);
    }

    public function paginate($perPage=10)
    {
        return $this->productModel->paginate($perPage);
    }

    public function where($conditions)
    {
        return $this->productModel->where($conditions);
    }

    public function find($id)
    {
        return $this->productModel->find($id);
    }
    public function getAllCategoriesOrderedByName()
    {
        return $this->categoryModel->orderBy('name', 'ASC')->get();
    }

    public function getAllBrandsOrderedByName()
    {
        return $this->brandModel->orderBy('name', 'ASC')->get();
    }

    public function getAllRatingsWithProductTitle()
    {
        return ProductRating::select('product_ratings.*', 'products.title as productTitle')
            ->orderBy('product_ratings.created_at', 'DESC')
            ->leftJoin('products', 'products.id', 'product_ratings.product_id')
            ->get();
    }
    public function findRatingById($id)
    {
        return ProductRating::find($id);
    }
}
