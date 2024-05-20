@if(Session::has('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
        <h5><i class="icon fas fa-ban"></i> error!</h5>{{Session::get('error')}}
    </div>
@endif
@if(Session::has('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
        <h5><i class="icon fas fa-check"></i> success!...</h5>{{Session::get('success')}}
    </div>
@endif
