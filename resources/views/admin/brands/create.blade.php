@extends('admin.layouts.app')
@section('content')
    <!-- Content Header (Page header) -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create Brands</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{route('brands.index')}}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form action="" method="post" id="brandForm" name="brandForm">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Name">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">Slug</label>
                                    <input type="text" name="slug" id="slug" class="form-control" placeholder="Slug">
                                    <p></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button type="submit" id="bttn" class="btn btn-primary">Create</button>
                    <a href="{{route('brands.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection
@section('customJs')
    <script>
        $("#brandForm").submit(function (event) {
            event.preventDefault();
            var element = $(this);
            $('#bttn[type=submit]').prop('disabled', true);
            $.ajax({
                url: '{{ route("brands.store") }}',  // Use the correct route for POST
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function (response) {
                    // Handle the success response
                    $('#bttn[type=submit]').prop('disabled', false);

                    if (response['status'] == true) {
                        window.location.href = "{{route('brands.index')}}";
                        $('#name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");

                        $('#slug').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");

                    } else {
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
                            $('#slug').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        }
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('Something went wrong', textStatus, errorThrown);
                    console.log(jqXHR.responseText); // Log the response text for more details
                }

            })

        });
        $('#name').change(function () {
            element = $(this);
            $('#bttn[type=submit]').prop('disabled', true);

            $.ajax({
                url: '{{ route("getSlug") }}',
                type: 'get',
                data: {title: element.val()},
                dataType: 'json',
                success: function (response) {
                    $('#bttn[type=submit]').prop('disabled', false);

                    if (response['status'] == true) {
                        $('#slug').val(response['slug']);
                    }
                }

            })
        });

    </script>
@endsection

