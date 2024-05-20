<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordEmail;
use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\order;
use App\Models\orderItem;
use App\Models\User;
use App\Models\Wishlists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login()
    {
        return view('front.account.login');

    }

    public function register()
    {
        return view('front.account.register');
    }

    public function processRegister(Request $request)
    {
        $validator = Validator::make($request->all(), ['name' => 'required|min:4',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:4|confirmed']);
        if ($validator->passes()) {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();
            session()->flash('success', 'you have registerd successfully');
            return response()->json(['status' => true,
                'message' => 'you have been registerd successfully']);

        } else {
            return response()->json(['status' => false,
                'errors' => $validator->errors()]);
        }

    }

    public function authinicate(Request $request)
    {
        $validator = Validator::make(request()->all(),
            ['email' => 'required|email',
                'password' => 'required']);

        if ($validator->passes()) {
            if (Auth::attempt(['email' => $request->email,
                'password' => $request->password], $request->get('remember'))) {

                //
                if (session()->has('url.intended')) {
                    return redirect(session()->get('url.intended'));
                }
                return redirect()->route('account.profile');

            } else {
//                session()->flash('error','your email or password is incorrect.');
                return redirect()->route('account.login')
                    ->withInput(request()->only('email'))
                    ->with('error', 'your email or password is incorrect.');
            }
        } else {
            return redirect()->route('account.login')
                ->withErrors($validator)
                ->withInput(request()->only('email'));
        }
    }

    public function profile(Request $request)

    {
        $userId=Auth::user()->id;
        $countries=Country::orderBy('name','ASC')->get();
        $user=User::where('id',Auth::user()->id)->first();
        $customerAddress=CustomerAddress::where('user_id',$userId)->first();
        return view('front.account.profile',[
            'user'=>$user,
            'countries'=>$countries,
            'customerAddress'=>$customerAddress
        ]);

    }
    public function updateProfile(Request $request)
    {
      $userId=Auth::user()->id;
      $validator=Validator::make($request->all(),[
         'name'=>'required',
          'email'=>'required|email|unique:users,email,'.$userId.',id',
      ]);
        if ($validator->passes()){
        $user=User::find($userId);
        $user->name=$request->name;
        $user->email=$request->name;
        $user->phone=$request->phone;
        $user->save();
        session()->flash('success','Your Personal Information is Updated Successfully');
            return response()->json([
              'message'=>'Your Personal Information is Updated Successfully'
            ]);
        } else{
            return response()->json([
               'status'=>true,
               'errors'=>$validator->errors()
            ]);
        }

    }
    public function updateAddress(Request $request)
    {
      $userId=Auth::user()->id;
      $validator=Validator::make($request->all(),[
          'first_name' => 'required',
          'last_name' => 'required',
          'email' => 'required|email',
          'country_id' => 'required',
          'address' => 'required',
          'city' => 'required',
          'zip' => 'required',
          'state' => 'required',
      ]);
      if ($validator->passes()) {
          CustomerAddress::updateOrCreate(
              ['user_id' => $userId],
              [
                  'user_id' => $userId,
                  'first_name' => $request->first_name,
                  'last_name' => $request->last_name,
                  'email' => $request->email,
                  'mobile' => $request->mobile,
                  'country_id' => $request->country_id,
                  'address' => $request->address,
                  'apartment' => $request->apartment,
                  'city' => $request->city,
                  'zip' => $request->zip,
                  'state' => $request->state,
              ]);
          session()->flash('success', 'Your Address Information is Updated Successfully');

          return response()->json([
              'message' => 'Your Address Information is Updated Successfully'
          ]);
      }else{
          return response()->json([
              'status'=>true,
              'errors'=>$validator->errors()
          ]);
      }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('account.login')
            ->with('success', 'you logged out successfully');

    }

    public function order(Request $request)
    {
        $user = Auth::user();
        $orders = order::where('user_id', $user->id)->orderBy('created_at', 'DESC')->get();

        return view('front.account.order', [
            'orders' => $orders
        ]);

    }

    public function orderDetails(Request $request, $id)
    {
        $user = Auth::user();
        $orders = order::where('user_id', $user->id)->where('id', $id)->first();
        $orderitems = orderItem::where('order_id', $id)->get();
        $orderitemsCount = orderItem::where('order_id', $id)->count();
        return view('front.account.orderDetails', [
            'orders' => $orders,
            'orderItems' => $orderitems,
            'orderItemsCount' => $orderitemsCount
        ]);
    }

    public function wishlist(Request $request)
    {
        $wishlists = Wishlists::where('user_id', Auth::user()->id)->with('product')->get();
        return view('front.account.wishlist', [
            'wishlists' => $wishlists
        ]);


    }
    public function removeProductFromWishlist(Request $request)
    {
      $wishlist=Wishlists::where('user_id',Auth::user()->id)
          ->where('product_id',$request->id)->first();
      if ($wishlist==null){
          session()->flash('error','product already removed');
          return response()->json([
             'status'=>true,
          ]);
      }else{
          Wishlists::where('user_id',Auth::user()->id)
              ->where('product_id',$request->id)->delete();
          session()->flash('success','product  removed successfully');
          return response()->json([
              'status'=>true,
          ]);
      }
    }
    public function showChangePassword()
    {
return view('front.account.changepassword');
    }
    public function changePassword(Request $request)
    {
        $validator=Validator::make($request->all(),[
           'old_password'=>'required',
           'new_password'=>'required|min:5|same:confirm_password',
           'confirm_password'=>'required',
        ]);
        if ($validator->passes()){
        $user=User::select('id','password')->where('id',Auth::user()->id)->first();
            if (!Hash::check($request->old_password,$user->password)){
                session()->flash('error','Your old password is incorrect, please try again');
                return response()->json([
                    'status'=>true,

                ]);
            }
            User::where('id',$user->id)->update([
                'password'=>Hash::make($request->new_password)
            ]);
            session()->flash('success','Your old password is changed successfully');
            return response()->json([
                'status'=>true,
                'message'=>'password changed successfully'
            ]);
        }else{
            return response()->json([
               'status'=>false,
               'errors'=>$validator->errors()
            ]);
        }
    }
    public function forgotPassword()
    {
            return view('front.account.forgotpassword');
    }
    public function ProcessForgotPassword(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'email'=>'required|email|exists:users,email'
        ]);
        if ($validator->fails()){
            return redirect()->route('front.forgotPassword')->withErrors($validator)->withInput();
        }
        $token=Str::random(60);
        \DB::table('password_reset_tokens')->where('email',$request->email)->delete();
        \DB::table('password_reset_tokens')->insert([
            'email'=>$request->email,
        'token'=>$token,
        'created_at'=>now()
     ]);
        //send mail here
        $user=User::where('email',$request->email)->first();
        $formData=[
          'token'=>$token,
          'user'=>$user,
            'mailSubject'=>'you have requested to reset your password'
        ];
        Mail::to($request->email)->send(new ResetPasswordEmail($formData));
        return redirect()->route('front.forgotPassword')->with('success','check your inbox to reset password ');
    }
    public function resetPassword(Request $request,$token)
    {
        $tokenExists=\DB::table('password_reset_tokens')->where('token',$token)->first();
        if ($tokenExists ==null){
            return redirect()->route('front.forgotPassword')->with('error','Invalid request');
        }
    return view('front.account.resetpassword',[
        'token'=>$token
    ]);
    }
    public function ProcessResetPassword(Request $request)
    {
        $token=$request->token;
        $tokenObj=\DB::table('password_reset_tokens')->where('token',$token)->first();
        if ($tokenObj ==null){
            return redirect()->route('front.forgotPassword')->with('error','Invalid request');
        }
        $user=User::where('email',$tokenObj->email)->first();

        $validator=Validator::make($request->all(),[
            'new_password'=>'required|min:5|same:confirm_password',
            'confirm_password'=>'required'
        ]);
        if ($validator->fails()){
            return redirect()->route('front.resetPassword',$token)->withErrors($validator);
        }
        User::where('id',$user->id)->update([
            'password'=>Hash::make($request->new_password)
        ]);
        \DB::table('password_reset_tokens')->where('email',$user->email)->delete();

        return  redirect()->route('account.login')->with('success','you have successfully updated your password');
    }


}
