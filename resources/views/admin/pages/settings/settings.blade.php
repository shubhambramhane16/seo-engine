@extends('admin.layout.default')

@section('settings','active menu-item-open')
@section('content')
<div class="card card-custom">

    <div class="card-body">
        <div class="mb-7">
            <div class="row align-items-center">
                <div id="accordion" class="setting-accord">
                    <div class="card sett-card">
                        <div class="card-header" id="headingone">
                            <h2 class="mb-0">
                                <button class="d-flex align-items-center justify-content-between btn btn-link collapsed" data-toggle="collapse" data-target="#collapseone" aria-expanded="false" aria-controls="collapseone">
                                    Basic Details
                                    <span class="fa-stack fa-2x" id="sett-icon">
                                        <i class="fas fa-plus fa-stack-1x"></i>
                                    </span>
                                </button>
                            </h2>
                        </div>
                        <div id="collapseone" class="collapse" aria-labelledby="headingone">
                            <div class="card-body" style="padding: 20px 5px;">
                                <form method="POST" action="" class="w-100">
                                    {{ csrf_field() }}
                                    <div class="col-lg-9 col-xl-12">
                                        <div class="row align-items-center">
                                            <div class="form-group col-md-6">
                                                <label>Registered Office Address</label>
                                                <input type="hidden" name="setting_id" value="{{$details->id}}" class="form-control">
                                                <input type="text" name="registered_office_address" value="{{$details->registered_office_address ?? $details->registered_office_address}}" isrequired="required" class="form-control" placeholder="Enter Registered Office Address"> <br />
                                                <input type="text" name="registered_office_address2" value="{{$details->registered_office_address2 ?? $details->registered_office_address2}}" class="form-control" placeholder="Enter Registered Office Address2">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Office Address</label>
                                                <input type="text" name="office_address" value="{{$details->office_address ?? $details->office_address}}" isrequired="required" class="form-control" placeholder="Enter Office Address"> <br />
                                                <input type="text" name="office_address2" value="{{$details->office_address2 ?? $details->office_address2}}" class="form-control" placeholder="Enter Office Address2">
                                            </div>

                                            <div class="form-group col-md-12">
                                                <label>Phone Number</label>
                                                <div><input type="text" name="phone_number" value="{{$details->phone_number ?? $details->phone_number}}" isrequired="required" class="form-control" placeholder="Enter Phone Number"></div>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label>Email Address</label>
                                                <div><input type="email" name="email_id" value="{{$details->email_id ?? $details->email_id}}" isrequired="required" class="form-control" placeholder="Enter Email Address"></div>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label>WhatsApp No</label>
                                                <div>
                                                    <input type="text" name="whatsapp" value="{{$details->whatsapp ?? $details->whatsapp}}" isrequired="required" class="form-control" placeholder="Enter WhatsApp No">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label>Customer Care</label>
                                                <div>
                                                    <input type="text" name="customer_care" value="{{$details->customer_care ?? $details->customer_care}}" isrequired="required" class="form-control" placeholder="Enter Customer Care">
                                                </div>
                                            </div>

                                            <div class="form-group col-md-12">
                                                <center><input type="submit" name="basic_details" value='Update'class="btn btn-success"></center>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card sett-card">
                        <div class="card-header" id="headingTwo">
                            <h2 class="mb-0">
                                <button class="d-flex align-items-center justify-content-between btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Application
                                    <span class="fa-stack fa-2x" id="sett-icon">
                                        <i class="fas fa-plus fa-stack-1x"></i>
                                    </span>
                                </button>
                            </h2>
                        </div>
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo">
                            <div class="card-body" style="padding: 20px 5px;">
                                <form method="POST" action="" class="w-100">
                                    {{ csrf_field() }}
                                    <div class="col-lg-9 col-xl-12">
                                        <div class="row align-items-center">
                                            <div class="form-group col-md-12">
                                                <label>Website URL</label>
                                                <div>
                                                    <input type="hidden" name="setting_id" value="{{$details->id}}" class="form-control">
                                                    <input type="text" name="website_url" value="{{$details->website_url}}" class="form-control" placeholder="Enter Website Url">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>License key</label>
                                                <div>
                                                    <input type="text" name="licence_key" value="{{$details->licence_key}}" class="form-control" placeholder="Enter License Key">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Valid till</label>
                                                <div>

                                                    <input type="date" name="valid_till" value="{{$details->valid_till}}" class="form-control" placeholder="Enter Valid Till.">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Application Name</label>
                                                <div>

                                                    <input type="text" name="application_name" value="{{$details->application_name}}" class="form-control" placeholder="Enter Application Name.">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Secret Key</label>
                                                <div>

                                                    <input type="text" name="secret_key" value="{{$details->secret_key}}" class="form-control" placeholder="Enter Secret Key.">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <center><input type="submit" name="application" class="btn btn-success" value="Update"></center>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card sett-card">
                        <div class="card-header" id="headingThree">
                            <h2 class="mb-0">
                                <button class="d-flex align-items-center justify-content-between btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    API
                                    <span class="fa-stack fa-2x" id="sett-icon">
                                        <i class="fas fa-plus fa-stack-1x"></i>
                                    </span>
                                </button>
                            </h2>
                        </div>
                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree">
                            <div class="card-body" style="padding: 20px 5px;">
                                <form action="" method="POST" class="w-100">
                                    {{ csrf_field() }}
                                    <div class="col-lg-9 col-xl-12">
                                        <div class="row align-items-center">
                                            <div class="form-group col-md-12">
                                                <label>User Name</label>
                                                <div>
                                                    <input type="hidden" name="setting_id" value="{{$details->id}}" class="form-control">
                                                    <input type="text" name="user_name" value="{{$details->user_name}}" class="form-control" placeholder="Enter User Name">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Salt</label>
                                                <div>

                                                    <input type="text" name="salt" value="{{$details->salt}}" class="form-control" placeholder="Enter Salt">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Base URL</label>
                                                <div>
                                                    <input type="text" name="base_url" value="{{$details->base_url}}" class="form-control" placeholder="Enter Base Url.">
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <hr>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <h1 style="font-size: 22px;">Sync API's</h1>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Base URL</label>
                                                <div>
                                                    <input type="text" name="base_url2" value="{{$details->base_url2}}" class="form-control" placeholder="Enter Base Url.">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>City API Path</label>
                                                <div>

                                                    <input type="text" name="city_api_path" value="{{$details->city_api_path}}" class="form-control" placeholder="City API Path.">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Centre/Franchises API Path</label>
                                                <div>

                                                    <input type="text" name="centre_api_path" value="{{$details->centre_api_path}}" class="form-control" placeholder="Centre/Franchises API Path.">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Locality API Path</label>
                                                <div>

                                                    <input type="text" name="locality_api_path" value="{{$details->locality_api_path}}" class="form-control" placeholder="Locality API Path.">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Items/Product API Path</label>
                                                <div>
                                                    <input type="text" name="item_api_path" value="{{$details->item_api_path}}" class="form-control" placeholder="Items/Product API Path.">
                                                </div>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <center><input type="submit" class="btn btn-success" name="ApiButton" value="Update"></center>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
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
