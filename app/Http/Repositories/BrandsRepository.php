<?php

namespace App\Http\Repositories;

use App\Http\Controllers\Controller;
use App\Models\Brands;

class BrandsRepository extends Controller
{
    private Brands $model;

    public function __construct()
    {
        $this->model = new Brands();
    }

    public function latest()
    {
        return $this->model->latest('id');
    }

    public function where($conditions)
    {
        return $this->model->where($conditions);
    }

    public function searchByName($keyword)
    {
        return $this->model->where('name', 'like', '%' . $keyword . '%')->paginate(10);
    }

    public function paginate($perPage = 10)
    {
        return $this->model->paginate($perPage);

    }
}
