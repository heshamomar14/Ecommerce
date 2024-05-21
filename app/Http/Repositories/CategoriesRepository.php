<?php

namespace App\Http\Repositories;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoriesRepository extends Controller
{
    private Category $model;

    public function __construct()
    {
        $this->model = new Category();
    }

    public function latest()
    {
        return $this->model->latest('id');

    }
    public function searchByName($keyword)
    {
        return $this->model->where('name', 'like', '%' . $keyword . '%')->paginate(10);
    }
    public function find($id)
    {
        return $this->model->find($id);
    }

    public function where($conditions)
    {
        return $this->model->where($conditions);
    }
    public function paginate($perPage = 10)
    {
        return $this->model->paginate($perPage);
    }
}
