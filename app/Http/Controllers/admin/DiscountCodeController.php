<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;

//use App\Models\Category;
use App\Models\DiscountCoupon;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class DiscountCodeController extends Controller
{
    public function index(Request $request)
    {
        $coupons = DiscountCoupon::latest();
        if (!empty($request->get('keyword'))) {
            $coupons = $coupons->where('name', 'like', '%' . $request->get('keyword') . '%');
        }
        $coupons = $coupons->paginate(10);

        return view('admin.coupon.list', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupon.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), ['code' => 'required', 'type' => 'required', 'discount_amount' => 'required|numeric', 'status' => 'required']);
        if ($validator->passes()) {
            if (!empty($request->starts_at)) {
                $now = Carbon::now();
                $starts_at = Carbon::createFromFormat('Y-m-d H:i:s', $request->starts_at);

                if ($starts_at->lte($now) == true) {
                    return response()->json(['status' => false, 'errors' => ['starts_at' => 'start date can not be less than current time']]);
                }
            }
            if (!empty($request->starts_at) && !empty($request->expires_at)) {
                $starts_at = Carbon::createFromFormat('Y-m-d H:i:s', $request->starts_at);
                $expires_at = Carbon::createFromFormat('Y-m-d H:i:s', $request->expires_at);

                if ($expires_at->gt($starts_at) == false) {
                    return response()->json(['status' => false, 'errors' => ['expires_at' => 'expire date must be greater than start date']]);
                }
            }
            $discountCoupon = new DiscountCoupon();
            $discountCoupon->code = $request->code;
            $discountCoupon->name = $request->name;
            $discountCoupon->description = $request->description;
            $discountCoupon->max_uses = $request->max_uses;
            $discountCoupon->max_uses_user = $request->max_uses_user;
            $discountCoupon->type = $request->type;
            $discountCoupon->discount_amount = $request->discount_amount;
            $discountCoupon->min_amount = $request->min_amount;
            $discountCoupon->status = $request->status;
            $discountCoupon->starts_at = $request->starts_at;
            $discountCoupon->expires_at = $request->expires_at;
            $discountCoupon->save();

            session()->flash('success', 'coupon added successfully');
            return response()->json(['status' => true, 'message' => 'coupon added successfully']);

        } else {
            return response()->json(['status' => false, 'errors' => $validator->errors()]);
        }
    }

    public function edit(Request $request, $id)
    {
        $coupon = DiscountCoupon::find($id);
        if ($coupon == null) {
            session()->flash('error', 'record not found');
            return redirect()->route('coupon.index');
        }

        return view('admin.coupon.edit', ['coupon' => $coupon]);
    }

    public function update(Request $request, $id)
    {
        $discountCoupon = DiscountCoupon::find($id);
        if ($discountCoupon == null) {
            session()->flash('error', 'record not found');
            return redirect()->route('coupon.index');
        }
        $validator = Validator::make($request->all(), ['code' => 'required', 'type' => 'required', 'discount_amount' => 'required|numeric', 'status' => 'required']);
        if ($validator->passes()) {

            $discountCoupon->code = $request->code;
            $discountCoupon->name = $request->name;
            $discountCoupon->description = $request->description;
            $discountCoupon->max_uses = $request->max_uses;
            $discountCoupon->max_uses_user = $request->max_uses_user;
            $discountCoupon->type = $request->type;
            $discountCoupon->discount_amount = $request->discount_amount;
            $discountCoupon->min_amount = $request->min_amount;
            $discountCoupon->status = $request->status;
            $discountCoupon->starts_at = $request->starts_at;
            $discountCoupon->expires_at = $request->expires_at;
            $discountCoupon->save();

            session()->flash('success', 'coupon updated successfully');
            return response()->json(['status' => true, 'message' => 'coupon updated successfully']);

        } else {
            return response()->json(['status' => false, 'errors' => $validator->errors()]);
        }

    }

    public function destroy($couponId, Request $request)
    {

        $coupon = DiscountCoupon::find($couponId);

        if (empty($coupon)) {
            $request->session()->flash('error', 'coupon not found ');
            return response()->json(['status' => true, 'message' => 'coupon not found']);
        }

        $coupon->delete();
        $request->session()->flash('success', 'coupon deleted successfully');
        return response()->json(['status' => true, 'message' => 'coupon deleted successfully']);

    }
}
