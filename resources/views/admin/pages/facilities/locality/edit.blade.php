@extends('admin.layout.default')

@section('citymaster','active menu-item-open')
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
                                <label>State</label>
                                <select name="state_id" class="form-control" isrequired="isrequired">
                                    <option value="">Select State</option>
                                    @if($states)
                                    @foreach($states as $key => $list)
                                    <option value="{{$list->id}},{{$list->name}}" {{runTimeSelection($list->id,$cityDetail->state_id)}}>{{$list->name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label>City</label>
                                <div><input type="text" name="name" placeholder="Enter City Name" class="form-control" value="{{$cityDetail->name}}" isrequired="isrequired"></div>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Description</label>
                                <textarea name="description" id="" placeholder="Enter Description" class="form-control" cols="30" rows="10">{{$cityDetail->description}}</textarea>
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