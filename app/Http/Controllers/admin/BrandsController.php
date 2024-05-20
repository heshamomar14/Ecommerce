<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Repositories\BrandsRepository;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use App\Models\Brands;

class BrandsController extends Controller
{
    public BrandsRepository $brandsRepository;

    public function __construct()
    {
        $this->brandsRepository = new BrandsRepository;
    }


    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $brands= $this->brandsRepository->latest();
        if(!empty($request->get('keyword'))){
            $brands=$brands->where('name','like','%'.$request->get('keyword').'%');
        }
        $brands=$brands->paginate(10);
        return view('admin.brands.list',compact('brands'));
    }
    public function create()
    {
        return view('admin.brands.create');
    }
    public function store( Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:brands',
        ]);
        if ($validator->passes()) {
            $brands = new Brands();
            $brands->name = $request->name;
            $brands->slug = $request->slug;
            $brands->save();

            $request->session()->flash('success', 'brand added successfuly');
            return response()->json([
                'status' => true,
                'message' => 'brand added successfuly'
            ]);

        } else {
            return response()->json([
                'status' => true,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function edit($id,Request $request)
    {
        $brand=Brands::find($id);
        if (empty($brand)){
            return session()->flash('error','revord not found');
            return redirect()->route('brands.index');

        }
        $data['brand']=$brand;
        return view('admin.brands.edit',$data);
    }
    public function update($id,Request $request)
    {
        $brands=Brands::find($id);
        if (empty($brands)){
            return session()->flash('error','Record not found');
        return response()->json([
         'notfound'=>'true'
    ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug'=>'required|unique:brands,slug,'.$brands->id.',id',
        ]);
        if ($validator->passes()) {
            $brands->name = $request->name;
            $brands->slug = $request->slug;
            $brands->save();

            $request->session()->flash('success', 'brand added successfuly');
            return response()->json([
                'status' => true,
                'message' => 'brand added successfuly'
            ]);

        } else {
            return response()->json([
                'status' => true,
                'errors' => $validator->errors()
            ]);
        }

    }
    public function destroy($id,Request $request)
    {

        $brand = Brands::find($id);

        if (empty($brand)){
            $request->session()->flash('error','brand not found ');
            return response()->json([
                'status'=>true,
                'message'=>'brand not found'
            ]);
        }

        $brand->delete();
        $request->session()->flash('success','brand deleted successfuly');
        return response()->json([
            'status'=>true,
            'message'=>'brand deleted successfuly'
        ]);

    }


}
