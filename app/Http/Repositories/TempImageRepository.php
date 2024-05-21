<?php

namespace App\Http\Repositories;

use App\Http\Controllers\Controller;
use App\Models\TempImage;

class TempImageRepository extends Controller
{
    private TempImage $model;

    public function __construct()
    {
        $this->model = new TempImage();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }
}
