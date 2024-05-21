<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\DiscountCoupon;
use App\Models\order;
use App\Models\orderItem;
use App\Models\Product;
use App\Models\ShippingCharges;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartContoller extends Controller
{
    public function addToCart(Request $request)
    {

        $product = Product::find($request->id);
        if ($product == null) {
            return response()->json(['status' => false, 'message' => 'product not found']);
        }
        if (Cart::count() > 0) {

            $cartContent = Cart::content();
            $productAlreadyExist = false;

            foreach ($cartContent as $item) {
                if ($item->id == $product->id) {
                    $productAlreadyExist = true;
                }
            }
            if ($productAlreadyExist == false) {
                Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
                $status = true;
                $message = $product->title . 'is added to  your cart';
                session()->flash('success', $message);
            } else {
                $status = false;
                $message = $product->title . '  already added to  your cart';

            }
        } else {
            Cart::add($product->id, $product->title, 1, $product->price, ['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
            $status = true;
            $message = '(' . $product->title . ') is  already added to  your cart';
        }
        return response()->json(['status' => $status, 'message' => $message]);
    }

    public function cart()
    {
        $cartContent = Cart::content();
        $data['cartContent'] = $cartContent;
        return view('front.cart', $data);
    }

    public function updateCart(Request $request)
    {
        $rowId = $request->rowId;
        $qty = $request->qty;

        $itemInfo = Cart::get($rowId);
        $product = Product::find($itemInfo->id);
        //check the qty in the stock

        if ($product->track_qty == 'Yes') {
            if ($qty <= $product->qty) {
                Cart::update($rowId, $qty);
                $message = 'cart updated successfully';
                $status = true;
            } else {
                $message = 'Requested qty (' . $qty . ') is not available in the stock';
                $status = false;
                session()->flash('error', $message);

            }
        } else {
            Cart::update($rowId, $qty);
            $message = 'cart updated successfully';
            $status = true;

        }
        session()->flash('success', $message);
        return response()->json(['status' => true, 'message' => 'cart updated successfully']);
    }

    public function deleteItem(Request $request)
    {
        $itemInfo = Cart::get($request->rowId);
        if ($itemInfo == null) {
            $errormessage = 'item is not found';
            session()->flash('error', 'Item is not found in the cart');
            return response()->json(['status' => false, 'message' => $errormessage]);
        }
        Cart::remove($request->rowId);
        $message = 'Item deleted successfully from cart';
        session()->flash('success', $message);
        return response()->json(['status' => true, 'message' => $message]);
    }

    public function checkOut()
    {
        $discount = 0;

        // if the cart is empty redirect to the cart page
        if (Cart::count() == 0) {
            return redirect()->route('front.cart');
        }
        // if user is not logged in then redirect to the login page
        if (Auth::check() == false) {
            if (!session()->has('url.intended')) {
                session(['url.intended' => url()->current()]);
            }
            return redirect()->route('account.login');
        }
        session()->forget('url.intended');
        $customerAddress = CustomerAddress::where('user_id', Auth::user()->id)->first();
        $countries = Country::orderBy('name', 'ASC')->get();

        //shiiping (choose country to post charges in checkout)
        if ($customerAddress != '') {
            $userCountry = $customerAddress->country_id;
            $shippingInfo = ShippingCharges::where('country_id', $userCountry)->first();
            if ($shippingInfo !== null) {
                $totalqty = 0;
                $grandtotal = 0;

                foreach (Cart::content() as $item) {
                    $totalqty += $item->qty;
                }
                $totalshipping = $totalqty * $shippingInfo->amount;
                $grandtotal = Cart::subtotal(2, '.', '') + $totalshipping;
            } else {
                $totalshipping = 0;
                $grandtotal = Cart::subtotal(2, '.', '');
            }
        } else {
            $grandtotal = Cart::subtotal(2, '.', '');
            $totalshipping = 0;

        }

        return view('front.checkout', ['countries' => $countries, 'customerAddress' => $customerAddress, 'totalshipping' => $totalshipping, 'grandtotal' => $grandtotal, 'discount' => $discount]);
    }


    public function processCheckOut(Request $request)
    {
// make validation for check out data

        $validator = Validator::make($request->all(), ['first_name' => 'required', 'last_name' => 'required', 'email' => 'required|email', 'country' => 'required', 'address' => 'required', 'city' => 'required', 'zip' => 'required', 'state' => 'required',]);
        // if validate has error
        if ($validator->fails()) {
            return response()->json(['message' => 'please fix the errors', 'status' => false, 'errors' => $validator->errors()]);
        }

        $user = Auth::user();
        CustomerAddress::updateOrCreate(['user_id' => $user->id], ['user_id' => $user->id, 'first_name' => $request->first_name, 'last_name' => $request->last_name, 'email' => $request->email, 'mobile' => $request->mobile, 'country_id' => $request->country, 'address' => $request->address, 'apartment' => $request->apartment, 'city' => $request->city, 'zip' => $request->zip, 'state' => $request->state,]);
        if ($request->payment_method == 'cod') {
            $shippingInfo = ShippingCharges::where('country_id', $request->country)->first();
            $subtotal = Cart::subtotal(2, '.', '');
            $shipping = 0;
            $discount = 0;
            $discountCodeId = '';
            $promocode = '';
            $grandtotal = $subtotal + $shipping;
            $totalqty = 0;
            if (session()->has('code')) {
                $code = session()->get('code');

                if ($code->type == 'percentage') {
                    $discount = ($code->discount_amount / 100) * $subtotal;
                } else {
                    $discount = $code->discount_amount;
                }
                $discountCodeId = $code->id;
                $promocode = $code->code;
            }


            foreach (Cart::content() as $item) {
                $totalqty += $item->qty;
            }
            if ($shippingInfo != null) {
                $shippingCharge = $totalqty * $shippingInfo->amount;
                $grandtotal = ($subtotal - $discount) + $shippingCharge;

            } else {
                $shippingInfo = ShippingCharges::where('country_id', 'rest_of_world')->first();
                $shippingCharge = $totalqty * $shippingInfo->amount;
                $grandtotal = ($subtotal - $discount) + $shippingCharge;

            }


            //save order
            $order = new  order();
            $order->subtotal = $subtotal;
            $order->shipping = $shippingCharge;
            $order->grand_total = $grandtotal;
            $order->discount = $discount;
            $order->coupon_code_id = $discountCodeId;
            $order->payment_status = 'not paid';
            $order->status = 'pending';
            $order->coupon_code = $promocode;
            $order->user_id = $user->id;
            $order->first_name = $request->first_name;
            $order->last_name = $request->last_name;
            $order->email = $request->email;
            $order->address = $request->address;
            $order->state = $request->state;
            $order->city = $request->city;
            $order->zip = $request->zip;
            $order->notes = $request->notes;
            $order->mobile = $request->mobile;
            $order->country_id = $request->country;
            $order->save();

            foreach (Cart::content() as $item) {
                $orderItem = new orderItem();
                $orderItem->product_id = $item->id;
                $orderItem->order_id = $order->id;
                $orderItem->name = $item->name;
                $orderItem->qty = $item->qty;
                $orderItem->price = $item->price;
                $orderItem->total = $item->price * $item->qty;
                $orderItem->save();

                $productData = Product::find($item->id);
                if ($productData->track_qty == 'Yes') {
                    $currentQty = $productData->qty;
                    $updatedQty = $currentQty - $item->qty;
                    $productData->qty = $updatedQty;
                    $productData->save();
                }

            }


            session()->flash('success', 'you have successfully placed your order');
            Cart::destroy();
            session()->forget('code');
            return response()->json(['message' => 'order saved successfully', 'orderId' => $order->id, 'status' => true]);
        } else {

        }
    }

    public function thankYou($id)
    {
        return view('front.thankyou', ['id' => $id]);
    }

    public function applyDiscount(Request $request)
    {
        $code = DiscountCoupon::where('code', $request->code)->first();
        if ($code == null) {
            return response()->json(['status' => false, 'message' => 'Invalid coupon']);
        }
        $couponUsedByUser = order::where(['coupon_code_id' => $code->id, 'user_id' => Auth::user()->id])->count();
        if ($couponUsedByUser >= $code->max_uses_user) {
            return response()->json(['status' => false, 'message' => 'you already used this coupon']);
        }

        $subtotal = Cart::subtotal(2, '.', '');
        if ($code->min_amount > 0) {
            if ($subtotal < $code->min_amount) {
                return response()->json(['status' => false, 'message' => 'your min amount is must be $' . $code->min_amount . 'to take the Discount on total invoice']);
            }
        }
        $now = Carbon::now();
        session()->put('code', $code);
        return $this->getOrderSummery($request);
    }

    public function getOrderSummery(Request $request)
    {
        $subtotal = Cart::subtotal(2, '.', '');
        $discount = 0;
        $discountstring = '';
        if (session()->has('code')) {
            $code = session()->get('code');

            if ($code->type == 'percentage') {
                $discount = ($code->discount_amount / 100) * $subtotal;
            } else {
                $discount = $code->discount_amount;
            }
            $discountstring = '<div class="mt-4" id="discount_response">
                        <strong>' . session()->get('code')->code . '</strong>
                            <a class="btn btn-sm btn-danger" id="remove_coupon"> <i class="fa fa-times"></i></a>
                        </div>';
        }
        if ($request->country_id > 0) {
            $shippingInfo = ShippingCharges::where('country_id', $request->country_id)->first();
            $totalqty = 0;
            foreach (Cart::content() as $item) {
                $totalqty += $item->qty;
            }
            if ($shippingInfo != null) {
                $shippingCharge = $totalqty * $shippingInfo->amount;
                $grandtotal = ($subtotal - $discount) + $shippingCharge;
                return response()->json(['shippingCharge' => number_format($shippingCharge, 2), 'grandtotal' => number_format($grandtotal, 2), 'status' => true, 'discount' => $discount, 'discountstring' => $discountstring]);
            } else {
                $shippingInfo = ShippingCharges::where('country_id', 'rest_of_world')->first();
                $shippingCharge = $totalqty * $shippingInfo->amount;
                $grandtotal = ($subtotal - $discount) + $shippingCharge;
                return response()->json(['shippingCharge' => number_format($shippingCharge, 2), 'grandtotal' => number_format($grandtotal, 2), 'status' => true, 'discount' => $discount, 'discountstring' => $discountstring]);
            }
        } else {
            return response()->json(['shippingCharge' => 0, 'grandtotal' => $subtotal, 'status' => true]);
        }

    }

    public function removeDiscount(Request $request)
    {
        session()->forget('code');
        return $this->getOrderSummery($request);

    }
}
