<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Repositories\BrandsRepository;
use App\Models\Brands;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $brands = $this->brandsRepository->latest()->paginate();
        if (!empty($request->get('keyword'))) {
            $brands = $this->brandsRepository->searchByName($request->get('keyword'));
        }
//        $brands =$this->brandsRepository->paginate();
        return view('admin.brands.list', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), ['name' => 'required', 'slug' => 'required|unique:brands',]);
        if ($validator->passes()) {
            $brands = new Brands();
            $brands->name = $request->name;
            $brands->slug = $request->slug;
            $brands->save();

            $request->session()->flash('success', 'brand added successfully');
            return response()->json(['status' => true, 'message' => 'brand added successfully']);

        } else {
            return response()->json(['status' => true, 'errors' => $validator->errors()]);
        }
    }

    public function edit($id, Request $request)
    {
        $brand = Brands::find($id);
        if (empty($brand)) {
            session()->flash('error', 'revord not found');
            return redirect()->route('brands.index');

        }
        $data['brand'] = $brand;
        return view('admin.brands.edit', $data);
    }

    public function update($id, Request $request)
    {
        $brands = Brands::find($id);
        if (empty($brands)) {
            session()->flash('error', 'Record not found');
            return response()->json(['notfound' => 'true']);
        }
        $validator = Validator::make($request->all(), ['name' => 'required', 'slug' => 'required|unique:brands,slug,' . $brands->id . ',id',]);
        if ($validator->passes()) {
            $brands->name = $request->name;
            $brands->slug = $request->slug;
            $brands->save();

            $request->session()->flash('success', 'brand added successfully');
            return response()->json(['status' => true, 'message' => 'brand added successfully']);

        } else {
            return response()->json(['status' => true, 'errors' => $validator->errors()]);
        }

    }

    public function destroy($id, Request $request)
    {

        $brand = Brands::find($id);

        if (empty($brand)) {
            $request->session()->flash('error', 'brand not found ');
            return response()->json(['status' => true, 'message' => 'brand not found']);
        }

        $brand->delete();
        $request->session()->flash('success', 'brand deleted successfully');
        return response()->json(['status' => true, 'message' => 'brand deleted successfully']);

    }


}
