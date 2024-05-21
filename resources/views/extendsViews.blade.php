// for admin
@extends('admin.layouts.app')
@section('content')

@endsection
//for java section
@section('customJs')
    <script>
        $("#").change(function(){
            $.ajax({
                url:'',
                type:'',
                data:{},
                dataType:'',
                success: function(response){

                }
            });
        });

    </script>

@endsection

// for shop
@extends('front.layouts.app')
@section('content')

@endsection

// ajax method example

