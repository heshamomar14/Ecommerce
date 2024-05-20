@extends('admin.layouts.app')
@section('content')
    <!-- Content Header (Page header) -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            @include('admin.message')
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit SubCategory</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{route('subCategories.index')}}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        @include('admin.message')

        <!-- Default box -->
        <div class="container-fluid">
            <form action="" method="post" id="categoryForm" name="categoryForm">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="name">Category</label>
                                    <select name="category" id="category" class="form-control">
                                        <option value=""> select  a category</option>
                                        @if($categories->isNotEmpty())
                                            @foreach($categories as $category)
                                                <option {{($subcategory->category_id==$category->id)?'selected':''}} value="{{$category->id}}">
                                                    {{$category->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" value="{{$subcategory->name}}"
                                     name="name"  id="name" class="form-control" placeholder="Name">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">Slug</label>
                                    <input type="text" value="{{$subcategory->slug}}" readonly name="slug" id="slug" class="form-control" placeholder="Slug">
                                    <p></p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select  name="status" id="status" class="form-control">
                                        <option {{($subcategory->status==1)?'selected':''}} value="1">Active</option>
                                        <option {{($subcategory->status==0)?'selected':''}} value="0">Block</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Show on Home</label>
                                    <select  name="showHome" id="showHome" class="form-control">
                                        <option {{($category->showHome==1)?'selected':''}} value="Yes">Yes</option>
                                        <option {{($category->showHome==0)?'selected':''}} value="No">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button  type="submit" id="button" class="btn btn-primary">update</button>
                    <a href="{{route('subCategories.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection
@section('customJs')
    <script>
        $("#categoryForm").submit(function(event) {
            event.preventDefault();
            var element = $(this);
$('#button[type=submit]').prop('disabled',true);
            $.ajax({
                url: '{{ route("subCategories.update",$subcategory->id) }}',  // Use the correct route for POST
                type: 'put',
                data: element.serializeArray(),
                dataType: 'json',
                success: function (response) {
                    // Handle the success response
                    $('#button[type=submit]').prop('disabled',false);

                    if (response['status'] == true) {
                        window.location.href="{{route('subCategories.index')}}";
                        $('#name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");

                        $('#slug').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");

                    } else {
                        if (response['notFound']==true){
                            window.location.href='{{route('subCategories.index')}}';
                        }
                        var errors = response['errors'];
                        if (errors['name']) {
                            $('#name').addClass('is-invalid')
                                .siblings('p').addClass('invalid-feedback').html(errors['name']);
                        } else {
                            $('#name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        }
                        if (errors['slug']) {
                            $('#slug').addClass('is-invalid')
                                .siblings('p').addClass('invalid-feedback').html(errors['slug']);
                        } else {
                            $('#slug').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback') .html("");
                        }
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('Something went wrong', textStatus, errorThrown);
                    console.log(jqXHR.responseText); // Log the response text for more details
                }

            })

        });
$('#name').change(function (){
    element=$(this);
    $('#bttn[type=submit]').prop('disabled',true);

    $.ajax({
        url: '{{ route("getSlug") }}',
        type: 'get',
        data: {title: element.val()},
        dataType: 'json',
        success: function (response) {
            $('#bttn[type=submit]').prop('disabled',false);

            if (response['status']==true){
    $('#slug').val(response['slug']);
}
        }

    })
});

    </script>
@endsection
