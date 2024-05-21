<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;


class TempImageController extends Controller
{
    public function create(Request $request)
    {
        $image = $request->image;
        if (!empty($image)) {
            $ext = $image->getClientOriginalExtension();
            $newname = time() . '.' . $ext;
            $tempimage = new TempImage();
            $tempimage->name = $newname;
            $tempimage->save();
            $image->move(public_path() . '/temp', $newname);
            $sourcePath = public_path() . '/temp/' . $newname;
            $destPath = public_path() . '/temp/thumb/' . $newname;
            $image = Image::read($sourcePath);
            $image->resize(300, 275);
            $image->save($destPath);
            return response()->json(['status' => true, 'image_id' => $tempimage->id, 'ImagePath' => asset('temp/thumb/' . $newname), 'message' => 'image upload successfuly']);
        }
    }
}
