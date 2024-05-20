<?php

namespace App\Http\Controllers;

use App\Http\Controllers\admin\PageController;
use App\Mail\OrderEmail;
use App\Mail\sendContactEmail;
use App\Models\order;
use App\Models\Page;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class FrontController extends Controller
{
    public function index()
    {
        $products=Product::where('is_featured','Yes')->where('status',1)->get();
$data['featuredProducts']=$products;
$latestProducts=Product::orderBy('id','DESC')->where('status',1)->take(8)->get();
        $data['latestProducts']=$latestProducts;

        return view('front.home',$data);
    }
public function addToWishlists(Request $request)
{
if (Auth::check()==false){
    return response()->json([
       'status'=>false ,
        'message'=>'you must log in first'

    ]);
    return redirect()->route('account.login');
}

//call product
$product=Product::where('id',$request->id)->first();
if ($product==null){
    return response()->json([
        'status'=>false,
        'message'=>'<div class="alert alert-danger">Product not found</div>'
    ]);
}
//    $wishlist= new Wishlists;
//    $wishlist->user_id=Auth::user()->id;
//    $wishlist->product_id=$request->id;
//    $wishlist->save();
    Wishlists::updateOrCreate([
        'user_id'=>Auth::user()->id,
        'product_id'=>$request->id
    ],[
        'user_id'=>Auth::user()->id,
        'product_id'=>$request->id
    ]);
return response()->json([
   'status'=>true,
   'message'=>'<div class="alert alert-success"><strong>"'.$product->title.'"</strong> added to wishlist</div>'
]);
}
public function page($slug)
{
$page=Page::where('slug',$slug)->first();
return view('front.page',[
    'page'=>$page
]);
}
public function sendContactEmail (Request $request)
{
$validator=Validator::make($request->all(),[
   'name'=>'required',
    'email'=>'required|email',
    'subject'=>'required|min:10'
]);
if ($validator->passes()){
            //send email here ??

    $mailData=[
        'name'=>$request->name,
        'email'=>$request->email,
        'subject'=>$request->subject,
        'message'=>$request->message,
        'mailSubject'=>' <<< you have received a contact email >>>'
    ];
    $admin=User::where('id',1)->first();

    Mail::to($admin->email)->send(new sendContactEmail($mailData));
    session()->flash('success','thanks for contacting us , we will back to you soon.');
    return response()->json([
        'status'=>true,
    ]);

}else{
    return response()->json([
       'status'=>false,
        'errors'=>$validator->errors()
    ]);
}
}
}
