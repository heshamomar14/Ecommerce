@extends('front.layouts.app')
@section('content')
    <section class="container">
        <div class="col-md-12 text-center py-5">
            @if(Session::has('success'))
                <div class=" alert alert-success">
                    {{Session::get('success')}}
                </div>
            @endif
            <p>Thank You !</p>
            <p>Your Order ID Is : {{$id}}</p>
        </div>
    </section>
@endsection
