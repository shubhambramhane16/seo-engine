@extends('admin.layout.default')

@section('templatesmaster', 'active menu-item-open')
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
                                    <label>Template Name</label>
                                    <div><input type="text" name="template_name" placeholder="Enter Template Name"
                                            class="form-control" value="{{ old('template_name') }}"></div>
                                </div>
                                <div class="form-group col-md-12">
                                    <label>Select Template</label>
                                    <input type="file" name="file" placeholder="Enter Template" class="form-control"
                                        value="{{ old('file') }}">
                                </div>


                                <div class="form-group col-md-12">
                                    <label>Description</label>
                                    <textarea name="description" id="" placeholder="Enter Description" class="form-control" cols="30"
                                        rows="5">{{ old('description') }}</textarea>
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

@endsection

{{-- Scripts Section --}}
@section('scripts')
@endsection
