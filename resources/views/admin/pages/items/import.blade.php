@extends('admin.layout.default')

@section('itemsmaster','active menu-item-open')
@section('content')

<style>
    .margin-top-25 {
        margin-top: 25px;
    }

    .padding-left-0 {
        padding-left: 0px;
    }
</style>
<div class="card card-custom">
    <div class="card-body">
        <div class="mb-7">
            <div class="row align-items-center">

                <form method="POST" action="" class="w-100" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="col-lg-12">
                        <div class="row align-items-center">

                            <div class="form-group col-md-12 mb-0">
                                <h5 class="mb-0">Import Test</h5>
                                <hr>
                            </div>
                            <div class="form-group col-md-12 mb-0">
                                <a href="{{url('/')}}/public/uploads/excel_samples/items_sample.csv" class="float-right btn-sm btn btn-info" target="_blank" style="position: absolute;right: 20px;">Download Import Sample</a>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Choose Excel File</label>
                                <input type="file" name="uploaded_file" value="" isrequired="required" class="form-control" placeholder="">
                            </div>

                            <div class="form-group col-md-12">
                                <div class="text-center"><button class="btn btn-success">Submit</button></div>
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
<link rel="stylesheet" href="{{ asset('multiselect/bootstrap.multiselect.css') }}" />
@endsection

{{-- Scripts Section --}}
@section('scripts')
@endsection
