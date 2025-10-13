@extends('admin.layout.default')

@section('centermaster','active menu-item-open')
@section('content')
<div class="card card-custom">

    <div class="card-body">
        <div class="mb-7">
            <div class="row align-items-center">

                <form method="POST" action="" class="w-100" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="col-lg-9 col-xl-12">
                        <div class="row align-items-center">
                            <div class="form-group col-md-12 mb-0">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label>Centre Type</label>
                                        <div>
                                            <select name="centre_type" class="form-control" isrequired="required">
                                                <option value="">Centre Type</option>
                                                <option value="1" {{runTimeSelection(1,old('centre_type'))}}>Collection Centre</option>
                                                <option value="2" {{runTimeSelection(2,old('centre_type'))}}>Laboratory</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Centre Name</label>
                                <div><input type="text" name="centre_name" class="form-control" value="{{old('centre_name')}}" placeholder="Enter Centre Name" isrequired="required"></div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Centre Display Name</label>
                                <div><input type="text" name="display_name" class="form-control" value="centre" placeholder="Enter Centre Display Name" isrequired="required"></div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Centre Phone</label>
                                <div>
                                    <input type="text" name="phone" class="form-control" value="{{old('phone')}}" placeholder="Enter Centre Phone" maxlength="10" minlength="10">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Centre Email</label>
                                <div><input type="text" name="email" class="form-control" value="{{old('email')}}" placeholder="Enter Centre Email"></div>
                            </div>
                            <div class="form-group col-md-12">
                                <hr>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Address</label>
                                <input type="text" name="address_line1" class="form-control" value="{{old('address_line1')}}" placeholder="Address line 1" isrequired="required">
                            </div>
                            <div class="form-group col-md-12">
                                <input type="text" name="address_line2" class="form-control" value="{{old('address_line2')}}" placeholder="Address line 2">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Google Navigation (Latitude)</label>
                                <div><input type="text" name="centre_lat" value="{{old('centre_lat')}}" class="form-control" placeholder="Enter Latitude"></div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Google Navigation (Longitude)</label>
                                <div><input type="text" name="centre_lng" value="{{old('centre_lng')}}" class="form-control" placeholder="Enter Longitude"></div>
                            </div>
                            <div class="form-group col-md-6">
                                <select class="form-control" name="state_id" id="state" isrequired="required">
                                    <option value="">Select State</option>
                                    @if($states)
                                    @foreach($states as $state)
                                    <option value="{{$state->id}}" data-name="{{$state->name}}" {{runTimeSelection($state->id,old('state_id'))}}>{{$state->name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <select class="form-control" name="city_id" id="city" isrequired="required">
                                    <option value="">Select City</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <input type="text" name="locality" class="form-control" value="{{old('locality')}}" placeholder="Locality">
                            </div>
                            <div class="form-group col-md-6">
                                <input type="text" name="landmark" class="form-control" value="{{old('landmark')}}" placeholder="Enter Landmark">
                            </div>
                            <div class="form-group col-md-6">
                                <input type="text" name="pincode" class="form-control number" value="{{old('pincode')}}" placeholder="Enter Pincode" isrequired="required" maxlength="6">
                            </div>
                            <div class="form-group col-md-12">
                                <hr>
                            </div>
                            <!-- <div class=" col-md-12">
                                <label>Contact Information</label>
                            </div>
                            <div class="form-group col-md-6">
                                <input type="text" name="mobile" class="form-control" placeholder="Enter Mobile" isrequired="required">
                            </div>
                            <div class="form-group col-md-6">
                                <input type="text" name="landline" class="form-control" placeholder="Enter Land Line Number" isrequired="required">
                            </div>
                            <div class="form-group col-md-6">
                                <input type="text" name="email" class="form-control" placeholder="Enter Email" isrequired="required">
                            </div> -->
                            <!-- <div class="form-group col-md-12">
                                <hr>
                            </div> -->
                            <div class=" col-md-12">
                                <label>Centre Timing</label>
                            </div>
                            <div class="form-group col-md-12">
                                @include('admin.layout.partials.extras.centre-timing')
                            </div>
                            <div class="form-group col-md-12">
                                <label>About Centre</label>
                                <textarea id="editor1" class="form-control" name="about_us" id="" cols="30" rows="10" placeholder="About Centre"></textarea>
                            </div>
                            <div class="form-group col-md-12">
                                <hr>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Facilities </label> <span>(will show which are only available)</span>
                                <select class="form-control" name="centre_facilities[]" id="facilities" multiple isrequired="required">
                                    @if($facilities)
                                    @foreach($facilities as $facility)
                                    <option value="{{$facility->id}}" {{runTimeSelection($facility->id,old('state_id'))}}>{{$facility->facility_name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Centre Images <small>(Width:1400px, Height: 550px) (Multiple allowed)</small></label>
                                <div><input type="file" name="centre_images[]" multiple class="form-control"></div>
                            </div>

                            <div class="form-group col-md-12">
                                <hr>
                            </div>
                            <div class=" col-md-12">
                                <label>Centre Head </label>
                            </div>
                            <div class="form-group col-md-6">
                                <input type="text" name="head_name" value="{{old('head_name')}}" class="form-control" placeholder="Enter Name">
                            </div>
                            <div class="form-group col-md-6">
                                <input type="text" name="head_mobile" maxlength="10" value="{{old('head_mobile')}}" class="form-control number" placeholder="Enter Mobile Number">
                            </div>
                            <div class="form-group col-md-6">
                                <input type="text" name="head_email" value="{{old('head_email')}}" class="form-control" placeholder="Enter Email Address">
                            </div>
                            <div class="form-group col-md-12">
                                <hr>
                            </div>
                            <div class=" col-md-12">
                                <label>Contract Details </label>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">Upload PDF <small>(Multiple selection allowed)</small></label>
                                <input type="file" name="contract_document[]" class="form-control" multiple>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">Document Details</label>
                                <input type="text" name="contract_details[]" class="form-control" placeholder="Enter Document Details">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">Document Type</label>
                                <input type="text" name="contract_document_type[]" class="form-control" placeholder="Enter Document Type">
                            </div>
                            <div class="form-group col-md-12">
                                <hr>
                            </div>
                            <div class=" col-md-12">
                                <label>SEO</label>
                            </div>
                            <div class="form-group col-md-12">
                                <input type="text" name="seo_title" value="{{old('seo_title')}}" class="form-control" placeholder="Enter Title">
                            </div>
                            <div class="form-group col-md-12">
                                <textarea class="form-control" name="seo_description" id="" cols="30" rows="10" placeholder="Enter Description">{{old('seo_description')}}</textarea>
                            </div>
                            <div class="form-group col-md-12">
                                <textarea class="form-control" name="seo_keywords" id="" cols="30" rows="10" placeholder="Enter Keywords">{{old('seo_keywords')}}</textarea>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <center><button class="btn btn-success">Submit</button></center>
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
<style>
    hr {
        border-top: 1px solid rgb(0 0 0 / 35%);
    }
</style>
<link rel="stylesheet" href="{{ asset('multiselect/bootstrap.multiselect.css') }}" />
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('multiselect/bootstrap.multiselect.js')}}"></script>

<script>
    $(document).ready(function() {
        $('#days').multiselect({
            nonSelectedText: 'Select Opening Days',
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '100%'
        });
        $('#facilities').multiselect({
            nonSelectedText: 'Select Facilities',
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '100%'
        });
    });
</script>
<script src="https://ckeditor.com/docs/vendors/4.11.3/ckeditor/ckeditor.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function() {
        CKEDITOR.replace('editor1');
    });
</script>
@endsection
