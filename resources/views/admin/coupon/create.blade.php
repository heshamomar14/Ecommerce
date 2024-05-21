@extends('admin.layouts.app')
@section('content')
    <!-- Content Header (Page header) -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Create coupon code</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{route('coupon.index')}}" class="btn btn-primary">Back</a>
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
            <form action="" method="post" enctype="multipart/form-data" id="discountForm" name="discountForm">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">code</label>
                                    <input type="text" name="code" id="code" class="form-control"
                                           placeholder="Coupon code">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">name</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                           placeholder="Coupon code name">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">Description</label>
                                    <textarea class="form-control" name="description" id="description" cols="30"
                                              rows="5"> </textarea>
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Max Uses</label>
                                    <input type="text" name="max_uses" id="max_uses" class="form-control"
                                           placeholder="Max Uses">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Max Uses user</label>
                                    <input type="text" name="max_uses_user" id="max_uses_user" class="form-control"
                                           placeholder="Max Uses User">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Type</label>
                                    <select name="type" id="type" class="form-control">
                                        <option value="percentage">Percentage</option>
                                        <option value="fixed">Fixed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Discount Amount</label>
                                    <input type="text" name="discount_amount" id="discount_amount" class="form-control"
                                           placeholder="Discount Amount">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Min Amount</label>
                                    <input type="text" name="min_amount" id="min_amount" class="form-control"
                                           placeholder="Min Amount">
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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="">Start At</label>
                                    <input autocomplete="off" type="text" name="starts_at" id="starts_at"
                                           class="form-control" placeholder="Start At">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="">Expires At</label>
                                    <input autocomplete="off" type="text" name="expires_at" id="expires_at"
                                           class="form-control" placeholder="Expires At">
                                    <p></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pb-5 pt-3">
                    <button type="submit" id="bttn" class="btn btn-primary">Create</button>
                    <a href="{{route('coupon.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection
@section('customJs')
    <script>
        $("#discountForm").submit(function (event) {
            event.preventDefault();
            var element = $(this);
            $('#bttn[type=submit]').prop('disabled', true);
            $.ajax({
                url: '{{ route("coupon.store") }}',  // Use the correct route for POST
                type: 'post',
                data: element.serializeArray(),
                dataType: 'json',
                success: function (response) {
                    // Handle the success response
                    $('#bttn[type=submit]').prop('disabled', false);

                    if (response['status'] == true) {
                        window.location.href = "{{route('coupon.index')}}";
                        $('#code').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        $('#discount_amount').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        $('#starts_at').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");
                        $('#expires_at').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html("");

                    } else {
                        var errors = response['errors'];
                        if (errors['code']) {
                            $('#code').addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback')
                                .html(errors['code']);
                        } else {
                            $('#code').removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback')
                                .html("");
                        }
                        if (errors['discount_amount']) {
                            $('#discount_amount').addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback')
                                .html(errors['discount_amount']);
                        } else {
                            $('#discount_amount').removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback')
                                .html("");
                        }
                        if (errors['starts_at']) {
                            $('#starts_at').addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback')
                                .html(errors['starts_at']);
                        } else {
                            $('#starts_at').removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback')
                                .html("");
                        }
                        if (errors['expires_at']) {
                            $('#expires_at').addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback')
                                .html(errors['expires_at']);
                        } else {
                            $('#expires_at').removeClass('is-invalid')
                                .siblings('p')
                                .removeClass('invalid-feedback')
                                .html("");
                        }
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('Something went wrong', textStatus, errorThrown);
                    console.log(jqXHR.responseText); // Log the response text for more details
                }

            })

        });
        {{--$('#name').change(function () {--}}
        {{--    element = $(this);--}}
        {{--    $('#bttn[type=submit]').prop('disabled', true);--}}

        {{--    $.ajax({--}}
        {{--        url: '{{ route("getSlug") }}',--}}
        {{--        type: 'get',--}}
        {{--        data: {title: element.val()},--}}
        {{--        dataType: 'json',--}}
        {{--        success: function (response) {--}}
        {{--            $('#bttn[type=submit]').prop('disabled', false);--}}

        {{--            if (response['status'] == true) {--}}
        {{--                $('#slug').val(response['slug']);--}}
        {{--            }--}}
        {{--        }--}}

        {{--    })--}}
        {{--});--}}
        {{--Dropzone.autoDiscover = false;--}}
        {{--const dropzone = $("#image").dropzone({--}}
        {{--    init: function () {--}}
        {{--        this.on('addedfile', function (file) {--}}
        {{--            if (this.files.length > 1) {--}}
        {{--                this.removeFile(this.files[0]);--}}
        {{--            }--}}
        {{--        });--}}
        {{--    },--}}
        {{--    url: "{{ route('temp.uploadImg') }}",--}}
        {{--    maxFiles: 1,--}}
        {{--    paramName: 'image',--}}
        {{--    addRemoveLinks: true,--}}
        {{--    acceptedFiles: "image/jpeg,image/png,image/gif",--}}
        {{--    headers: {--}}
        {{--        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
        {{--    }, success: function (file, response) {--}}
        {{--        $("#image_id").val(response.image_id);--}}
        {{--    }--}}
        {{--});--}}
        $(document).ready(function () {
            $('#starts_at').datetimepicker({
                // options here
                format: 'Y-m-d H:i:s',
            });
        });
        $(document).ready(function () {
            $('#expires_at').datetimepicker({
                // options here
                format: 'Y-m-d H:i:s',
            });
        });
    </script>
@endsection
