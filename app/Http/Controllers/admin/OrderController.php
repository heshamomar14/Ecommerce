<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\order;
use App\Models\orderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders=order::latest('orders.created_at')->select('orders.*','users.name','users.email');
        $orders=$orders->leftJoin('users','users.id','orders.user_id');

        if ($request->get('keyword')!=""){
            //search in orders by user name or email or order id
            $orders=$orders->where('users.name','like','%'.$request->keyword.'%');
            $orders=$orders->orWhere('users.email','like','%'.$request->keyword.'%');
            $orders=$orders->orWhere('orders.id','like','%'.$request->keyword.'%');
        }
        $order=$orders->paginate(10);
            return view('admin.order.list',[
                'orders'=>$order
            ]);
    }
    public function detail($orderId,Request $request)
    {
        $order=order::select('orders.*','countries.name as countryName')
        ->where('orders.id',$orderId)
        ->leftJoin('countries','countries.id','orders.country_id')
        ->first();
        $orderItems=orderItem::where('order_id',$orderId)->get();
        return view('admin.order.detail',[
            'order'=>$order,
            'orderItems'=>$orderItems
        ]);
    }
    public function changeOrderStatus($orderId,Request $request)
    {
    $order=order::find($orderId);
    $order->status=$request->status;
    $order->shipped_date=$request->shipped_date;
    $order->save();
    session()->flash('success','Order Status is updated successfully');
    return response()->json([
       'status'=>true,
        'message'=>'Order Status is updated successfully'
    ]);
    }
    public function sendInvoiceEmail(Request $request,$orderId)
    {
    orderEmail($orderId,$request->userType);
        session()->flash('success','Order email sent successfully');
        return response()->json([
            'status'=>true,
            'message'=>'Order email sent successfully'
        ]);
    }
}
