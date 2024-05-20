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
}
