@php use Carbon\Carbon; @endphp
@extends('admin.layouts.app')
@section('content')
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Order: #{{$order->id}}</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{route('order.index')}}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            @include('admin.message')
            <div class="row">
                <div class="col-md-9">
                    @csrf
                    <div class="card">
                        <form method="get">
                            <div class="card-header pt-3">
                                <div class="row invoice-info">
                                    <div class="col-sm-4 invoice-col">
                                        <h1 class="h5 mb-3">Shipping Address</h1>
                                        <address>
                                            <strong>{{$order->first_name.' '.$order->last_name}}</strong><br>
                                            {{$order->address}}<br>
                                            {{$order->city}},{{$order->zip}}({{$order->countryName}})<br>
                                            Phone: {{$order->mobile}}<br>
                                            Email: {{$order->email}}
                                        </address>
                                        <strong>Shipped Date</strong>
                                        @if(!empty($orders->shipped_date))
                                            {{Carbon::parse($orders->shipped_date)->format('d M,Y')}}
                                        @else
                                            n/a
                                        @endif
                                    </div>
                                    <div class="col-sm-4 invoice-col">
                                        <b>Order ID:</b> {{$order->id}}<br>
                                        <b>Total:</b> ${{$order->grand_total}}<br>
                                        <b>Status:</b>
                                        @if($order->status=='pending')
                                            <span class="badge bg-danger">Pending</span>
                                        @elseif($order->status=='shipped')
                                            <span class="badge bg-info">Shipped</span>
                                        @elseif($order->status=='cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @else
                                            <span class="badge bg-success">Delivered</span>
                                        @endif
                                        <br>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="card-body table-responsive p-3">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Product</th>
                                    <th width="100">Price</th>
                                    <th width="100">Qty</th>
                                    <th width="100">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orderItems as $item)
                                    <tr>
                                        <td>{{$item->name}}</td>
                                        <td>$ {{$item->price}}</td>
                                        <td>{{$item->qty}}</td>
                                        <td>$ {{ number_format($item->total,2)}}</td>
                                    </tr>
                                @endforeach

                                <tr>
                                    <th colspan="3" class="text-right">Subtotal:</th>
                                    <td>$ {{ number_format($order->subtotal,2)}}</td>
                                </tr>

                                <tr>
                                <tr>
                                    <th colspan="3" class="text-right">
                                        Discount: {{(!empty($orders->coupon_code)?'('.$orders->coupon_code.')':'')}}</th>
                                    <td>$ {{ number_format($order->discount,2)}}</td>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right">Shipping:</th>
                                    <td>$ {{ number_format($order->shipping,2)}}</td>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right">Grand Total:</th>
                                    <td>$ {{ number_format($order->grand_total,2)}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <form action="" method="post" name="changeOrderStatusForm" id="changeOrderStatusForm">
                            <div class="card-body">
                                <h2 class="h4 mb-3">Order Status</h2>
                                <div class="mb-3">
                                    <select name="status" id="status" class="form-control">
                                        <option value="pending" {{($order->status=='pending')?'selected':''}} >Pending
                                        </option>
                                        <option value="shipped" {{($order->status=='shipped')?'selected':'' }}>Shipped
                                        </option>
                                        <option value="delivered" {{($order->status=='delivered')?'selected':''}}>
                                            Delivered
                                        </option>
                                        <option value="cancelled" {{($order->status=='cancelled') ?'selected':''}}>
                                            Cancelled
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="">Shipped Date</label>
                                    <input value="{{$order->shipped_date}}" type="text" id="shipped_date"
                                           name="shipped_date" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="post" name="sendInvoiceEmail" id="sendInvoiceEmail">
                                <h2 class="h4 mb-3">Send Inovice Email</h2>
                                <div class="mb-3">
                                    <select name="userType" id="userType" class="form-control">
                                        <option value="customer">Customer</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-primary">Send</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card -->
    </section>
@endsection
@section('customJs')
    <script>
        $(document).ready(function () {
            $('#shipped_date').datetimepicker({
                // options here
                format: 'Y-m-d H:i:s',
            });
        });
        $("#changeOrderStatusForm").submit(function (event) {
            event.preventDefault();
            $.ajax({
                url: '{{route('order.changeOrderStatus',$order->id)}}',
                type: 'post',
                data: $(this).serializeArray(),
                dataType: 'json',
                success: function (response) {
                    window.location.href = '{{route('order.details',$order->id)}}'
                }
            });
        });
        $("#sendInvoiceEmail").submit(function (event) {
            event.preventDefault();
            if (confirm("Are you want to send Email?")) {
                $.ajax({
                    url: '{{route('order.sendInvoiceEmail',$order->id)}}',
                    type: 'post',
                    data: $(this).serializeArray(),
                    dataType: 'json',
                    success: function (response) {
                        window.location.href = '{{route('order.details',$order->id)}}'
                    }
                });
            }

        });
    </script>
@endsection
