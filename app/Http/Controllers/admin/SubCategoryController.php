<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Repositories\CategoriesRepository;
use App\Http\Repositories\SubCategoriesRepository;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{


    public SubCategoriesRepository  $subCategoriesRepository;
    public function __construct()
    {
        $this->subCategoriesRepository = new SubCategoriesRepository();
    }

    public function index(Request $request)
    {
        $subcategories = $this->subCategoriesRepository->withCategories();
        $subcategories=$this->subCategoriesRepository->latest()->paginate();
        if (!empty($request->get('keyword'))) {

            $subcategories=$this->subCategoriesRepository->searchByNameOrCategoryName($request->get('keyword'));
        }
        return view('admin.subCategory.list', compact('subcategories'));
    }

    public function create()
    {
        $categories = $this->subCategoriesRepository->orderCategoriesByAscName();
        $data['categories'] = $categories;
        return view('admin.subCategory.create', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), ['name' => 'required', 'status' => 'required', 'category' => 'required', 'slug' => 'required|unique:sub_categories',]);
        if ($validator->passes()) {
            $subcategory = new SubCategory();
            $subcategory->name = $request->name;
            $subcategory->slug = $request->slug;
            $subcategory->status = $request->status;
            $subcategory->showHome = $request->showHome;
            $subcategory->category_id = $request->category;
            $subcategory->save();

            $request->session()->flash('success', 'subcategory added successfully');
            return response(['status' => true, 'message' => 'subcategory added successfully']);
        } else {
            return response(['status' => false, 'errors' => $validator->errors()]);
        }
    }


    public function edit($id, Request $request)
    {
        $subcategory = SubCategory::find($id);
        if (empty($subcategory)) {
            $request->session()->flash('error', 'record not found');
            return redirect(route('subCategories.index'));
        }
        $categories = Category::orderBy('name', 'ASC')->get();
        $data['categories'] = $categories;
        $data['subcategory'] = $subcategory;
        return view('admin.subCategory.edit', $data);
    }

    public function update($id, Request $request)
    {
        $subcategory = SubCategory::find($id);

        if (empty($subcategory)) {
            $request->session()->flash('error', 'Record not found');

            return response()->json(['status' => false, 'notFound' => true]);
        }

        $validator = Validator::make($request->all(), ['name' => 'required', 'slug' => 'required|unique:sub_categories,slug,' . $subcategory->id . ',id', 'status' => 'required', 'category' => 'required',]);

        if ($validator->passes()) {
            // Update the existing subcategory instead of creating a new one
            $subcategory->name = $request->name;
            $subcategory->slug = $request->slug;
            $subcategory->status = $request->status;
            $subcategory->showHome = $request->showHome;
            $subcategory->category_id = $request->category;
            $subcategory->save();

            $request->session()->flash('success', 'Subcategory updated successfully');

            return response(['status' => true, 'message' => 'Subcategory updated successfully']);
        } else {
            return response(['status' => false, 'errors' => $validator->errors()]);
        }
    }

    public function destroy($id, Request $request)
    {
        $subcategory = SubCategory::find($id);
        if (empty($subcategory)) {
            $request->session()->flash('error', ' subCategory not found ');
            return response()->json(['status' => true, 'message' => 'subcategory not found']);
        }
        $subcategory->delete();
        $request->session()->flash('success', ' subcategory deleted successfully');
        return response()->json(['status' => true, 'message' => 'subcategory deleted successfully']);

    }


}
