@extends('admin.layout.default')

@section('statemaster','active menu-item-open')
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
                                <label>State Name</label>
                                <div><input type="text" name="name" placeholder="Enter State Name" class="form-control" isrequired="isrequired" value="{{old('name')}}"></div>
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
