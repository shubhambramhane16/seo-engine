@extends('admin.layout.default')

@section('facilitymaster','active menu-item-open')
@section('content')
<div class="card card-custom">

    <div class="card-body">
        <div class="mb-7">
            <div class="row align-items-center">

                <form method="POST" action="" class="w-100" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="col-lg-9 col-xl-12">
                        <div class="row align-items-center">
                            <div class="form-group col-md-12">
                                <label>Facility Name</label>
                                <div><input type="text" name="facility_name" class="form-control" placeholder="Enter Facility Name" isrequired="required"></div>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Facility Icon <small>(Width:96px, Height: 96px)</small></label>
                                <div><input type="file" name="icon" class="form-control" isrequired="required"></div>
                            </div>
                            <div class="form-group col-md-12">
                                <center><button class="btn btn-success">Submit</button></center>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- Styles Section --}}
@section('styles')

@endsection

{{-- Scripts Section --}}
@section('scripts')
@endsection