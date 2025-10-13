@extends('admin.layout.default')

@section('enquirymaster', 'active menu-item-open')
@section('content')
    <div class="card card-custom">

        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">

                    <form method="POST" action="" class="w-100">
                        {{ csrf_field() }}
                        <div class="col-lg-9 col-xl-12">
                            <div class="row align-items-center">

                                <div class="form-group col-md-12">
                                    <label>Enquiry Name</label>
                                    <div><input type="text" name="name" placeholder="Enter Enquiry Name"
                                            class="form-control" isrequired="isrequired" value="{{ $enquiryDetail->name }}">
                                    </div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label>Enquiry Number</label>
                                    <div><input type="text" name="number" placeholder="Enter Enquiry Number"
                                            class="form-control" isrequired="isrequired"
                                            value="{{ $enquiryDetail->number }}"></div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label>Enquiry Slot Date </label>
                                    <div><input type="text" name="slot_date	" placeholder="Enter Enquiry Slot_date	"
                                            class="form-control"
                                            value="{{ $enquiryDetail->slot_date }}"></div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label>Enquiry Slot Time </label>
                                    <div><input type="text" name="slot_time	" placeholder="Enter Enquiry Slot_time	"
                                            class="form-control"
                                            value="{{ $enquiryDetail->slot_time }}"></div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label>Enquiry City</label>
                                    <div><input type="text" name="city" placeholder="Enter Enquiry City"
                                            class="form-control" value="{{ $enquiryDetail->city }}">
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label>Enquiry Locality</label>
                                    <div><input type="text" name="locality" placeholder="Enter Enquiry Locality"
                                            class="form-control"
                                            value="{{ $enquiryDetail->locality }}"></div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label>Enquiry Page</label>
                                    <div><input type="text" name="page" placeholder="Enter Enquiry Page"
                                            class="form-control" isrequired="isrequired" value="{{ $enquiryDetail->page }}">
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <label>Enquiry Item Id</label>
                                    <div><input type="text" name="item_id" placeholder="Enter Enquiry Item_id"
                                            class="form-control"
                                            value="{{ $enquiryDetail->item_id }}"></div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label>Enquiry Item Reference</label>
                                    <div><input type="text" name="item_reference" placeholder="Enter Enquiry Item_reference"
                                            class="form-control"
                                            value="{{ $enquiryDetail->item_reference }}"></div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label>Enquiry Form</label>
                                    <div><input type="text" name="form" placeholder="Enter Enquiry Form"
                                            class="form-control" isrequired="isrequired"
                                            value="{{ $enquiryDetail->form }}"></div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label>Enquiry Query</label>
                                    <div><input type="text" name="query" placeholder="Enter Enquiry Query"
                                            class="form-control"
                                            value="{{ $enquiryDetail->query }}"></div>
                                </div>

                                <div class="form-group col-md-12">
                                    <div class="text-center"><button class="btn btn-success">Update</button></div>
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
