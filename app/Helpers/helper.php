<?php

use App\Mail\OrderEmail;
use App\Models\Category;
use App\Models\Country;
use App\Models\order;
use App\Models\Page;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Mail;

function getCategories()
 {
 return Category::orderBy('name','ASC')
     ->where('showHome','Yes')->where('status',1)->orderBy('id','DESC')
     ->with('subcategories')->get();
 }

function getProductImages($productId)
{
return ProductImage::where('product_id',$productId)->first();
}

function orderEmail($orderId,$userType='customer')
{
    $order=Order::where('id',$orderId)->with('items')->first();
    if ($userType=='customer'){
        $subject='thanks for order!';
        $email=$order->email;

    }else{
        $subject='you have received an order ';
        $email=env('ADMIN_EMAIL');
    }
    $mailData=[
    'subject'=>$subject,
    'order'=>$order,
        'userType'=>$userType
    ];

    Mail::to($order->email)->send(new OrderEmail($mailData));
//    dd($order);

}
function getCountryInfo($id)
{
    Country::where('id',$id)->first();
}

function staticPages()
{
    $pages=Page::orderBy('name','ASC')->get();
    return $pages;
}
?>

