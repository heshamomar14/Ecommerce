@extends('front.layouts.app')
@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{route('front.home')}}">Home</a></li>
                    <li class="breadcrumb-item">Reset Password</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-10">
        <div class="container">
            @if(Session::has('success'))
                <div class="alert alert-success">
                    {{Session::get('success')}}
                </div>
            @endif   @if(Session::has('error'))
                <div class="alert alert-danger">
                    {{Session::get('error')}}
                </div>
            @endif
            <div class="login-form">
                <form action="{{route('front.ProcessResetPassword')}}" method="post">
                    @csrf
                    <h4 class="modal-title">Reset Password</h4>
                    <div class="form-group">
                        <input type="hidden" name="token" value="{{$token}}">
                        <input type="password" class="form-control   @error('new_password')is-invalid @enderror" placeholder="new_password"
                               required="required" name="new_password" value="">
                        @error('new_password')
                        <p class="is-invalid">{{$message}}</p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control   @error('confirm_password')is-invalid @enderror" placeholder="confirm_password"
                               required="required" name="confirm_password" value="">
                        @error('email')
                        <p class="is-invalid">{{$message}}</p>
                        @enderror
                    </div>
                    <input type="submit" class="btn btn-dark btn-block btn-lg" value="Update Password">
                </form>
            </div>
        </div>
    </section>
@endsection
