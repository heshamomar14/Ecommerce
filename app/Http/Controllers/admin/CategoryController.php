<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Media;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
//        phpinfo();
//        exit();
        $categories = Category::latest();
        //search in categories list
        if (!empty($request->get('keyword'))) {
            $categories = $categories->where('name', 'like', '%' . $request->get('keyword') . '%');
        }
        $categories = Category::latest()->paginate(10);

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

            ///get image from tempImg table
            if (!empty($request->image_id)) {
                //find my image id from temp Image model
                $tempimage = TempImage::find($request->image_id);
                $extArray = explode('.', $tempimage->name);
                $ext = last($extArray);
                //identify our path of images will upload
                $newImageName = $category->id . '.' . $ext;
                $sPath = public_path() . '/temp/' . $tempimage->name;
                $dPath = public_path() . '/uploads/category/' . $newImageName;
                File::copy($sPath, $dPath);
//                generate image and thumbing
                $dPath = public_path() . '/uploads/category/thumb/' . $newImageName;
                $img = Image::read($sPath);
                $img->resize(450, 600);
                $img->save($dPath);
                //save at database of categories
                $category->image = $newImageName;
                $category->save();
            }

            $request->session()->flash('success', 'category added successfuly');
            return response()->json(['status' => true, 'message' => 'category added successfuly']);
        } else {
            return response()->json(['status' => true, 'errors' => $validator->errors()]);
        }

    }

    public function edit($categoryId, Request $request)
    {
        $category = Category::find($categoryId);
        if (!empty($category)) {
            return view('admin.category.edit', compact('category'));
        }
    }

    public function update($categoryId, Request $request)
    {
        $category = Category::find($categoryId);
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

///save image from tempImg table
            if (!empty($request->image_id)) {
                //find my image id from temp Image model
                $tempimage = TempImage::find($request->image_id);
                $extArray = explode('.', $tempimage->name);
                $ext = last($extArray);
//identify our path of images will upload
                $newImageName = $category->id . '-' . time() . '.' . $ext;
                $sPath = public_path() . '/temp/' . $tempimage->name;
                $dPath = public_path() . '/uploads/category/' . $newImageName;
                File::copy($sPath, $dPath);
                //generate image and thumbing


                $dPath = public_path() . '/uploads/category/thumb' . $newImageName;
                $image = Image::read($sPath);
                $image->resize(450, 600);
                $image->save($dPath);
                //save at database of categories
                $category->image = $newImageName;
                $category->save();
                $oldimage = $category->image;
                //delete old image to update
                File::delete(public_path() . '/uploads/category/thumb' . $oldimage);
                File::delete(public_path() . '/uploads/category' . $oldimage);
            }
            $request->session()->flash('success', 'category updated successfuly');
            return response()->json(['status' => true, 'message' => 'category updated successfuly']);
        } else {
            return response()->json(['status' => false, 'errors' => $validator->errors()]);
        }
    }

    public function destroy($categoryId, Request $request)
    {

        $category = Category::find($categoryId);

        if (empty($category)) {
            $request->session()->flash('error', 'category not found ');
            return response()->json(['status' => true, 'message' => 'category not found']);
//                return redirect(route('categories.index'));
        }
        File::delete(public_path() . '/uploads/category/thumb' . $category->image);
        File::delete(public_path() . '/uploads/category' . $category->image);
        $category->delete();
        $request->session()->flash('success', 'category deleted successfuly');
        return response()->json(['status' => true, 'message' => 'category deleted successfuly']);

    }


}
