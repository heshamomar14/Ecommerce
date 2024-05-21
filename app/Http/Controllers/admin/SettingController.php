<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function showChangePassword()
    {
        return view('admin.changepassword');
    }

    public function ProcessChangePassword(Request $request)
    {
        $validator = Validator::make($request->all(), ['old_password' => 'required', 'new_password' => 'required|min:5|same:confirm_password', 'confirm_password' => 'required',]);
        $id = Auth::guard('admin')->user()->id;
        if ($validator->passes()) {
            $admin = User::select('id', 'password')->where('id', $id)->first();
            if (!Hash::check($request->old_password, $admin->password)) {
                session()->flash('error', 'Your old password is incorrect, please try again');
                return response()->json(['status' => true,

                ]);
            }
            User::where('id', $id)->update(['password' => Hash::make($request->new_password)]);
            session()->flash('success', 'Your  password is updated successfully');
            return response()->json(['status' => true, 'message' => 'password changed successfully']);
        } else {
            return response()->json(['status' => false, 'errors' => $validator->errors()]);
        }
    }
}
