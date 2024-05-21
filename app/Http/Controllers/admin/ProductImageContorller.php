<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class ProductImageContorller extends Controller
{
    public function update(Request $request)
    {
        $image = $request->image;
        if (!empty($image)) {
            $ext = $image->getClientOriginalExtension();
            $sourcePath = $image->getPathName();


            $productImage = new ProductImage();
            $productImage->product_id = $request->product_id;
            $productImage->image = 'NULL';
            $productImage->save();

            $imageName = $request->product_id . '-' . $productImage->id . '-' . time() . '.' . $ext;
            $productImage->image = $imageName;
            $productImage->save();

            $destPath = public_path() . '/uploads/product/large/' . $productImage->image;
            $image = Image::read($sourcePath);
            $image->resize(1400, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $image->save($destPath);
            $destPath = public_path() . '/uploads/product/small/' . $productImage->image;
            $image = Image::read($sourcePath);
            $image->resize(300, 300);
            $image->save($destPath);
            return response()->json(['status' => true, 'message' => 'image saved successfully', 'image_id' => $productImage->id, 'ImagePath' => asset('uploads/product/small/' . $productImage->image)]);
        }
    }

    public function destroy(Request $request)
    {
        $productsImage = ProductImage::find($request->id);
        if (empty($productsImage)) {
            return response()->json(['status' => false, 'message' => 'image not found']);


            File::delete(public_path('/uploads/product/large/') . $productImage->image);
            File::delete(public_path('/uploads/product/small/') . $productImage->image);
            $productsImage->delete();
            return response()->json(['status' => true, 'message' => 'image deleted done']);

        }
    }
}
