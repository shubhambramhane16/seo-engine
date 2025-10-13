@extends('admin.layout.default')

@section('pgoptions','active menu-item-open')
@section('content')
<div class="card card-custom">
    <!-- <div class="card-header flex-wrap border-0 pt-6 pb-0">
        <div class="card-title">
            <h3 class="card-label">Edit Category
            </h3>
        </div>
    </div> -->
    <div class="card-body">
        <div class="mb-7">
            <div class="row align-items-center">

                <form method="POST" action="" class="w-100" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="col-lg-9 col-xl-12">
                        <div class="row align-items-center">
                            <div class="form-group col-md-12">
                                <label>Payment Type Name</label>
                                <div><input type="text" name="option_name" class="form-control" value="{{$details->option_name}}" placeholder="Enter Payment Type Name" isrequired="required"></div>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Icon <small>(Width:96px, Height: 96px)</small></label>
                                @if($details['option_icon'])
                                <div class="_update_img_action">
                                    <a target="_black" href="{{asset('uploads/pgoptions/'.$details['option_icon'])}}" class="btn btn-success btn-sm">View</a> &nbsp;
                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" onclick="updateImage(this)">Update</a>
                                </div>
                                @endif
                                <div class="image_file _update_img_file" style="{{$details['option_icon'] ? 'display:none' : ''}}">
                                    <input type="file" name="icon" class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <center><button type="submit" class="btn btn-success">Update</button></center>
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