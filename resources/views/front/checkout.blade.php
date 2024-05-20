@extends('front.layouts.app')
@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="#">Home</a></li>
                    <li class="breadcrumb-item"><a class="white-text" href="#">Shop</a></li>
                    <li class="breadcrumb-item">Checkout</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="section-9 pt-4">
        @csrf
        <div class="container">
            <form action="" method="" id="orderform" name="orderform">
            <div class="row">
                <div class="col-md-8">
                    <div class="sub-title">
                        <h2>Shipping Address</h2>
                    </div>
                    <div class="card shadow-lg border-0">
                        <div class="card-body checkout-form">
                            <div class="row">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <input type="text" name="first_name" id="first_name" class="form-control" placeholder="First Name" value="{{(!empty($customerAddress))?$customerAddress->first_name:''}}">
                                        <p></p>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last Name" value="{{(!empty($customerAddress))?$customerAddress->last_name:''}}">
                                        <p></p>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <input type="text" name="email" id="email" class="form-control" placeholder="Email" value="{{(!empty($customerAddress))?$customerAddress->email:''}}">
                                        <p></p>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <select name="country" id="country" class="form-control">
                                            <option value="">Select a Country</option>
                                            @if($countries->isNotEmpty())
                                                @foreach($countries as $country)
                                                    <option {{(!empty($customerAddress)&&$customerAddress->country_id==$country->id)?'selected':''}} value="{{$country->id}}">{{$country->name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <textarea name="address" id="address" cols="30" rows="3" placeholder="Address" class="form-control" {{(!empty($customerAddress))?$customerAddress->address:''}}></textarea>
                                        <p></p>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <input type="text" name="apartment" id="apartment" class="form-control" placeholder="Apartment, suite, unit, etc. (optional) {{(!empty($customerAddress))?$customerAddress->apartment:''}}">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <input type="text" name="city" id="city" class="form-control" placeholder="City" {{(!empty($customerAddress))?$customerAddress->city:''}}>
                                        <p></p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <input type="text" name="state" id="state" class="form-control" placeholder="State" {{(!empty($customerAddress))?$customerAddress->state:''}}>
                                        <p></p>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <input type="text" name="zip" id="zip" class="form-control" placeholder="Zip" {{(!empty($customerAddress))?$customerAddress->zip:''}}>
                                        <p></p>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <input type="text" name="mobile" id="mobile" class="form-control" placeholder="Mobile No." {{(!empty($customerAddress))?$customerAddress->mobile:''}}>

                                    </div>
                                </div>


                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <textarea name="order_notes" id="order_notes" cols="30" rows="2" placeholder="Order Notes (optional)" class="form-control"></textarea>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="sub-title">
                        <h2>Order Summery</h2>
                    </div>
                    <div class="card cart-summery">
                        <div class="card-body">
                            @foreach(Cart::content() as $item)
                            <div class="d-flex justify-content-between pb-2">
                                <div class="h6">{{$item->name}} X {{$item->qty}}</div>
                                <div class="h6">${{$item->price}}</div>
                            </div>
                            @endforeach
                            <div class="d-flex justify-content-between summery-end">
                                <div class="h6"><strong>Subtotal</strong></div>
                                <div class="h6"><strong>${{Cart::subtotal()}}</strong></div>
                            </div>
                                <div class="d-flex justify-content-between summery-end">
                                <div class="h6"><strong>Discount</strong></div>
                                <div class="h6"><strong id="discount_value">${{$discount}}</strong></div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <div class="h6"><strong>Shipping</strong></div>
                                <div class="h6"><strong id="shippingCharge">${{number_format($totalshipping,2)}}</strong></div>
                            </div>
                            <div class="d-flex justify-content-between mt-2 summery-end">
                                <div class="h5"><strong>Total</strong></div>
                                <div class="h5"><strong id="grandtotal">${{$grandtotal}}</strong></div>
                            </div>
                        </div>
                    </div>
                    <div class="input-group apply-coupan mt-4">
                        <input type="text" id="code" name="code" placeholder="Coupon Code" class="form-control">
                        <button class="btn btn-dark" type="button" id="apply_coupon">Apply Coupon</button>
                    </div>
                    <div id="discount_response_wrapper">
                        @if(Session::has('code'))
                            <div class="mt-4" id="discount_response">
                                <strong>{{Session::get('code')->code}}</strong>
                                <a class="btn btn-sm btn-danger" id="remove_coupon"> <i class="fa fa-times"></i></a>
                            </div>
                        @endif
                    </div>

                    <div class="card payment-form ">
                        <h3 class="card-title h5 mb-3">Payment Methods</h3>
                        <div class="">
                            <input checked type="radio" name="payment_method" value="cod" id="payment_method_one">
                            <label for="payment_method_one" class="form-check-label" >COD</label>
                        </div>
                        <div class="">
                            <input type="radio" name="payment_method" value="stripe" id="payment_method_two">
                            <label for="payment_method_two" class="form-check-label" >Stripe</label>
                        </div>
                        <div class="card-body p-0 d-none mt-3" id="card-payment-form">
                            <div class="mb-3">
                                <label for="card_number" class="mb-2">Card Number</label>
                                <input type="text" name="card_number" id="card_number" placeholder="Valid Card Number" class="form-control">
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="expiry_date" class="mb-2">Expiry Date</label>
                                    <input type="text" name="expiry_date" id="expiry_date" placeholder="MM/YYYY" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label for="expiry_date" class="mb-2">CVV Code</label>
                                    <input type="text" name="expiry_date" id="expiry_date" placeholder="123" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="pt-4">
                            {{--                                <a href="#" class="btn-dark btn btn-block w-100">Pay Now</a>--}}
                            <button type="submit" class="btn-dark btn btn-block w-100">
                                Pay Now
                            </button>
                        </div>
                    </div>


                    <!-- CREDIT CARD FORM ENDS HERE -->

                </div>
            </div>

        </form>
    </div>
    </section>
@endsection
@section('customJs')
    <script type="text/javascript">
        $("#payment_method_one").click(function () {
            if ($(this).is(":checked") == true) {
                $("#card-payment-form").addClass('d-none');
            }
        });
        $("#payment_method_two").click(function () {
            if ($(this).is(":checked") == true) {
                $("#card-payment-form").removeClass('d-none');
            }
        });

        $("#orderform").submit(function (event) {
            event.preventDefault();
            $("#button[type='submit']").prop('disabled', true);

            $.ajax({
                url: '{{ route('front.processCheckOut') }}',
                type: 'post',
                data: $(this).serializeArray(),
                dataType: 'json', // Corrected 'datatype' to 'dataType'
                success: function (response) {
                    $("#button[type='submit']").prop('disabled', false);

                    var errors = response.errors;
                   if (response.status==false){
                       if (errors.first_name) {
                           $("#first_name").addClass('is-invalid')
                               .siblings("p")
                               .addClass('invalid-feedback')
                               .html(errors.first_name);
                       }else{
                           $("#first_name").removeClass('is-invalid')
                               .siblings("p")
                               .removeClass('invalid-feedback')
                               .html('');
                       }   if (errors.first_name) {
                           $("#last_name").addClass('is-invalid')
                               .siblings("p")
                               .addClass('invalid-feedback')
                               .html(errors.first_name);
                       }else{
                           $("#last_name").removeClass('is-invalid')
                               .siblings("p")
                               .removeClass('invalid-feedback')
                               .html('');
                       }   if (errors.first_name) {
                           $("#email").addClass('is-invalid')
                               .siblings("p")
                               .addClass('invalid-feedback')
                               .html(errors.first_name);
                       }else{
                           $("#email").removeClass('is-invalid')
                               .siblings("p")
                               .removeClass('invalid-feedback')
                               .html('');
                       }   if (errors.first_name) {
                           $("#country").addClass('is-invalid')
                               .siblings("p")
                               .addClass('invalid-feedback')
                               .html(errors.first_name);
                       }else{
                           $("#country").removeClass('is-invalid')
                               .siblings("p")
                               .removeClass('invalid-feedback')
                               .html('');
                       }   if (errors.first_name) {
                           $("#address").addClass('is-invalid')
                               .siblings("p")
                               .addClass('invalid-feedback')
                               .html(errors.first_name);
                       }else{
                           $("#address").removeClass('is-invalid')
                               .siblings("p")
                               .removeClass('invalid-feedback')
                               .html('');
                       }   if (errors.first_name) {
                           $("#city").addClass('is-invalid')
                               .siblings("p")
                               .addClass('invalid-feedback')
                               .html(errors.first_name);
                       }else{
                           $("#city").removeClass('is-invalid')
                               .siblings("p")
                               .removeClass('invalid-feedback')
                               .html('');
                       }   if (errors.first_name) {
                           $("#state").addClass('is-invalid')
                               .siblings("p")
                               .addClass('invalid-feedback')
                               .html(errors.first_name);
                       }else{
                           $("#state").removeClass('is-invalid')
                               .siblings("p")
                               .removeClass('invalid-feedback')
                               .html('');
                       } if (errors.first_name) {
                           $("#zip").addClass('is-invalid')
                               .siblings("p")
                               .addClass('invalid-feedback')
                               .html(errors.first_name);
                       }else{
                           $("#zip").removeClass('is-invalid')
                               .siblings("p")
                               .removeClass('invalid-feedback')
                               .html('');
                       }
                   }else {
                       window.location.href="{{url('/thanks/')}}/"+response.orderId;
                   }
                }
            });
        });
//change country
        $("#country").change(function(){
            $.ajax({
                url:'{{route('front.getOrderSummery')}}',
                type:'post',
                data:{country_id:$(this).val()},
                dataType:'json',
                success: function(response){
                $("#shippingCharge").html(response.shippingCharge)
                $("#grandtotal").html(response.grandtotal)
                }
            });
        });
        $("#apply_coupon").click(function(){
            $.ajax({
                url:'{{route('front.applyDiscount')}}',
                type:'post',
                data:{code: $("#code").val(),country_id: $("#country").val()},
                dataType:'json',
                success: function(response){
                     if (response.status==true){
                    $("#shippingCharge").html('$'+response.shippingCharge);
                    $("#grandtotal").html('$'+response.grandtotal);
                    $("#discount_value").html('$'+response.discount);
                    $("#discount_response_wrapper").html(response.discountstring);
            }else {
                         $("#discount_response_wrapper").html("<span class='text-danger'>"+response.message+"</span>");

                     }
                }
            });
        });
        $("body").on('click','#remove_coupon',function (){
            $.ajax({
                url:'{{route('front.removeDiscount')}}',
                type:'post',
                data:{country_id: $("#country").val()},
                dataType:'json',
                success: function(response){
                    if (response.status==true){
                        $("#shippingCharge").html('$'+response.shippingCharge);
                        $("#grandtotal").html('$'+response.grandtotal);
                        $("#discount_value").html('$'+response.discount);
                        $("#discount_response").html('');
                        $("#code").html('');
                    }
                }
            });
        })

    </script>
@endsection
