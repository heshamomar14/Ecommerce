<?php

namespace App\Http\Repositories;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;

class SubCategoriesRepository extends Controller
{
    private SubCategory $model;

    public function __construct()
    {
        $this->model = new SubCategory();
    }

    public function latest()
    {
        return $this->model->latest('id');

    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function where($conditions)
    {
        return $this->model->where($conditions);
    }
    public function withCategories()
    {
        return $this->model->with('category')->get();
    }
    public function orderCategoriesByAscName()
    {
        return $this->model->orderBy('name', 'ASC')->get();
    }
    public function searchByNameOrCategoryName($keyword)
    {
        return $this->model->where(function ($query) use ($keyword) {
            $query->where('name', 'like', '%' . $keyword . '%')
                ->orWhereHas('categories', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%' . $keyword . '%');
                });
        })->paginate(10);
    }
    public function paginate($perPage = 10)
    {
        return $this->model->paginate($perPage);
    }
}
