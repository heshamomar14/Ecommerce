<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\ShippingCharges;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    public function create()
    {
        $countries = Country::get();
        $data['countries'] = $countries;
        //select tha choosen countries that matches what was created
        $shippingcharges=ShippingCharges::select('shipping_charges.*','countries.name')
        ->leftjoin('countries','countries.id','shipping_charges.country_id')->get();

        $data['shippingcharges'] = $shippingcharges;
//dd($shippingcharges);
        return view('admin.shipping.create',$data);
    }

    public function store( Request $request)
    {
        $validators = Validator::make($request->all(),
            ['country' => 'required',
             'amount' => 'required|numeric'
            ]);

        if ($validators->passes()) {
            $count=ShippingCharges::where('country_id',$request->country)->count();
            if($count>0){
            session()->flash('error','shipping already added');
                    return response()->json(['status' => true]);
            }
            $shipping = new ShippingCharges();
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();
            session()->flash('success', 'shipping saved successfully ');
            return response()->json(['status' => true, 'message' => 'shipping saved successfully ']);
        } else {
            return response()->json(['status' => false, 'errors' => $validators->errors()]);
        }
    }
    public function edit($id)
    {
        $shippingCharge=ShippingCharges::find($id);
        $countries = Country::get();
        $data['countries'] = $countries;
        $data['shippingCharge'] = $shippingCharge;
        return view('admin.shipping.edit',$data);
    }
    public function update( $id,Request $request)
    {            $shipping = ShippingCharges::find($id);

        $validators = Validator::make($request->all(), ['country' => 'required', 'amount' => 'required|numeric']);

        if ($validators->passes()) {
            if ($shipping==null) {
                session()->flash('error', 'shipping not found ');
                return response()->json(['status' => true]);
            }
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();
            session()->flash('success', 'shipping updated successfully ');
            return response()->json(['status' => true, 'message' => 'shipping updated successfully ']);
        } else {
            return response()->json(['status' => false, 'errors' => $validators->errors()]);
        }
    }
    public function destroy($id, Request $request)
    {

        $shipping = ShippingCharges::find($id);

        if ($shipping==null) {
            session()->flash('error', 'shipping not found ');
            return response()->json(['status' => true]);
        }

        $shipping->delete();
        $request->session()->flash('success', 'shipping deleted successfuly');
        return response()->json(['status' => true, 'message' => 'shipping deleted successfuly']);

    }
}
