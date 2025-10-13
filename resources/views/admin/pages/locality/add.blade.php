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
                                @if($stateId!==null)
                                    <div class="form-group col-md-12">
                                        <label>State</label>
                                        <input type="hidden" name="state_id" value="{{$stateId}}" />
                                        <div><input type="text" name="state" placeholder="Enter State Name" class="form-control" value="{{getStateName($stateId)}}" isrequired="isrequired" readonly></div>
                                    </div>
                                @else
                                    <label>State</label>
                                    <select name="state_id" class="form-control" id="state" isrequired="isrequired">
                                        <option value="">Select State</option>
                                        @if($states)
                                        @foreach($states as $key => $list)
                                        <option value="{{$list->id}}" {{runTimeSelection($list->id,old('state_id'))}}>{{$list->name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                @endif
                               
                            </div>
                            <div class="form-group col-md-12">
                               
                                @if($cityId!==null)
                                    <div class="form-group col-md-12">
                                        <label>City</label>
                                        <input type="hidden" name="city_id"  value="{{$cityId}}"/>
                                        <div><input type="text"  placeholder="Enter City Name" class="form-control" value="{{getCityName($cityId)}}" isrequired="isrequired" readonly></div>
                                    </div>
                                @else
                                    <label>City</label>
                                    <select name="city_id" class="form-control" id="city" isrequired="isrequired">
                                        <option value="">Select City</option>
                                    </select>
                                @endif
                               
                                
                            </div>
                            <div class="form-group col-md-12">
                                <label>Locality</label>
                                <div><input type="text" name="name" placeholder="Enter Locality Name" class="form-control" isrequired="isrequired" value="{{old('name')}}"></div>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Description</label>
                                <textarea name="description" id="" placeholder="Enter Description" class="form-control" cols="30" rows="10">{{old('description')}}</textarea>
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