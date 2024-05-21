@extends('admin.layouts.app')
@section('content')
    <!-- Content Header (Page header) -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create User</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{route('users.index')}}" class="btn btn-primary">Back</a>
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
            <form action="" method="post" enctype="multipart/form-data" id="userForm" name="userForm">
                @csrf
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
                                    <label for="">Email</label>
                                    <input type="text" name="email" id="email" class="form-control">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="">Password</label>
                                    <input type="password" name="password" id="password" class="form-control">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone">Phone</label>
                                    <input type="text" name="phone" id="phone" class="form-control" placeholder="phone">
                                    <p></p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1">Active</option>
                                        <option value="0">Block</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button type="submit" id="bttn" class="btn btn-primary">Create</button>
                    <a href="{{route('users.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection
@section('customJs')
    <script>
        $("#userForm").submit(function (event) {
            event.preventDefault();
            var element = $(this);
            $('#bttn[type=submit]').prop('disabled', true);
            $.ajax({
                url: '{{ route("users.store") }}',  // Use the correct route for POST
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function (response) {
                    // Handle the success response
                    $('#bttn[type=submit]').prop('disabled', false);

                    if (response['status'] == true) {
                        window.location.href = "{{route('users.index')}}";
                        $('#name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");

                        $('#email').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        $('#password').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");

                    } else {
                        var errors = response['errors'];
                        if (errors['name']) {
                            $('#name').addClass('is-invalid')
                                .siblings('p').addClass('invalid-feedback').html(errors['name']);
                        } else {
                            $('#name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        }
                        if (errors['email']) {
                            $('#email').addClass('is-invalid')
                                .siblings('p').addClass('invalid-feedback').html(errors['email']);
                        } else {
                            $('#email').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        }
                        if (errors['password']) {
                            $('#password').addClass('is-invalid')
                                .siblings('p').addClass('invalid-feedback').html(errors['password']);
                        } else {
                            $('#password').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        }
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('Something went wrong', textStatus, errorThrown);
                    console.log(jqXHR.responseText); // Log the response text for more details
                }

            })

        });

    </script>
@endsection
