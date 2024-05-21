<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Repositories\BrandsRepository;
use App\Http\Repositories\CategoriesRepository;
use App\Http\Repositories\TempImageRepository;
use App\Models\Category;
use App\Models\Media;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;

class CategoryController extends Controller
{
    public CategoriesRepository $categoriesRepository;
    public TempImageRepository $imageRepository;

    public function __construct()
    {
        $this->categoriesRepository = new CategoriesRepository;
        $this->imageRepository = new TempImageRepository;
    }
    public function index(Request $request)
    {

        $categories =  $this->categoriesRepository->latest()->paginate();
        $keyword=$request->get('keyword');
        if (!empty($keyword)) {
            $categories = $this->categoriesRepository->searchByName($keyword);
        }
        return view('admin.category.list', compact('categories'));
    }

    public function create()
    {
        return view('admin.category.create');
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), ['name' => 'required', 'slug' => 'required|unique:categories',]);
        if ($validator->passes()) {
            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->showHome = $request->showHome;
            $category->save();

            if (!empty($request->image_id)) {
                //find my image id from temp Image model
                $tempimage = TempImage::find($request->image_id);
                $extArray = explode('.', $tempimage->name);
                $ext = last($extArray);
                $newImageName = $category->id . '.' . $ext;
                $sPath = public_path() . '/temp/' . $tempimage->name;
                $dPath = public_path() . '/uploads/category/' . $newImageName;
                File::copy($sPath, $dPath);
                $dPath = public_path() . '/uploads/category/thumb/' . $newImageName;
                $img = Image::read($sPath);
                $img->resize(450, 600);
                $img->save($dPath);
                //save at database of categories
                $category->image = $newImageName;
                $category->save();
            }

            $request->session()->flash('success', 'category added successfully');
            return response()->json(['status' => true, 'message' => 'category added successfully']);
        } else {
            return response()->json(['status' => true, 'errors' => $validator->errors()]);
        }

    }

    public function edit($categoryId, Request $request)
    {
        $category =  $this->categoriesRepository->find($categoryId);
        if (!empty($category)) {
            return view('admin.category.edit', compact('category'));
        }
    }

    public function update($categoryId, Request $request)
    {
        $category = $this->categoriesRepository->find($categoryId);
        if (empty($category)) {
            return response()->json(['status' => false, 'notfound' => true, 'message' => 'category not found']);
        }
        $validator = Validator::make($request->all(), ['name' => 'required', 'slug' => 'required|unique:categories,slug,' . $category->id . ',id',]);

        if ($validator->passes()) {
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->showHome = $request->showHome;
            $category->save();

            if (!empty($request->image_id)) {
                $tempimage = $this->imageRepository->find($request->image_id);
                $extArray = explode('.', $tempimage->name);
                $ext = last($extArray);
                $newImageName = $category->id . '-' . time() . '.' . $ext;
                $sPath = public_path() . '/temp/' . $tempimage->name;
                $dPath = public_path() . '/uploads/category/' . $newImageName;
                File::copy($sPath, $dPath);


                $dPath = public_path() . '/uploads/category/thumb' . $newImageName;
                $image = Image::read($sPath);
                $image->resize(450, 600);
                $image->save($dPath);
                $category->image = $newImageName;
                $category->save();
                $oldimage = $category->image;
                File::delete(public_path() . '/uploads/category/thumb' . $oldimage);
                File::delete(public_path() . '/uploads/category' . $oldimage);
            }
            $request->session()->flash('success', 'category updated successfully');
            return response()->json(['status' => true, 'message' => 'category updated successfully']);
        } else {
            return response()->json(['status' => false, 'errors' => $validator->errors()]);
        }
    }

    public function destroy($categoryId, Request $request)
    {

        $category = $this->categoriesRepository->find($categoryId);

        if (empty($category)) {
            $request->session()->flash('error', 'category not found ');
            return response()->json(['status' => true, 'message' => 'category not found']);
        }
        File::delete(public_path() . '/uploads/category/thumb' . $category->image);
        File::delete(public_path() . '/uploads/category' . $category->image);
        $category->delete();
        $request->session()->flash('success', 'category deleted successfully');
        return response()->json(['status' => true, 'message' => 'category deleted successfully']);

    }


}
