<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order email</title>
</head>
<body style="font-family: Arial,Helvetica,Sakkal Majalla ; font-size: 16px">
@if($mailData['userType']=='customer')
    <h1>Thanks for Your Order</h1>
    <h2>Your Order Id is:# {{$mailData['order']->id}}</h2>
@else
    <h1>you have recieved an order:</h1>
    <h2>Your Order Id is:# {{$mailData['order']->id}}</h2>
@endif
<address>
    <strong>{{$mailData['order']->first_name.' '.$mailData['order']->last_name}}</strong><br>
    {{$mailData['order']->address}}<br>
    {{$mailData['order']->city}},{{$mailData['order']->zip}}({{getCountryInfo($mailData['order']->country_id)}})<br>
    Phone: {{$mailData['order']->mobile}}<br>
    Email: {{$mailData['order']->email}}
</address>
<h2>Products</h2>
<table cellpadding="3" cellspacing="3" border="0" width="700">
    <thead>
    <tr style="background: #ccc;">

        <th>Product</th>
        <th width="100">Price</th>
        <th width="100">Qty</th>
        <th width="100">Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($mailData['order']->items as $item)
        <tr>
            <td>{{$item->name}}</td>
            <td>$ {{$item->price}}</td>
            <td>{{$item->qty}}</td>
            <td>$ {{ number_format($item->total,2)}}</td>
        </tr>
    @endforeach

    <tr>
        <th colspan="3" align="right" class="text-right">Subtotal:</th>
        <td>$ {{ number_format($mailData['order']->subtotal,2)}}</td>
    </tr>

    <tr>
    <tr>
        <th colspan="3" align="right" class="text-right">
            Discount: {{(!empty($mailData['order']->coupon_code)?'('.$mailData['order']->coupon_code.')':'')}}</th>
        <td>$ {{ number_format($mailData['order']->discount,2)}}</td>
    </tr>
    <tr>
        <th colspan="3" align="right" class="text-right">Shipping:</th>
        <td>$ {{ number_format($mailData['order']->shipping,2)}}</td>
    </tr>
    <tr>
        <th colspan="3" align="right" class="text-right">Grand Total:</th>
        <td>$ {{ number_format($mailData['order']->grand_total,2)}}</td>
    </tr>
    </tbody>
</table>

</body>
</html>
