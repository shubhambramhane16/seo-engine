@extends('admin.layout.default')

@section('rulemaster','active menu-item-open')
@section('content')
<div class="card card-custom">

    <div class="card-body">
        <div class="mb-7">
            <div class="row align-items-center">

                <form method="POST" action="" class="w-100">
                    {{ csrf_field() }}
                    <div class="col-lg-9 col-xl-12">
                        <div class="row align-items-center">


                            <div class="form-group col-md-4">
                                <label>Prefix</label>
                                <div>
                                    <input type="text" id="prefix" name="prefix" placeholder="Enter Prefix Name"
                                        class="form-control" isrequired="isrequired" value="{{ $ruleDetail->prefix }}">
                                </div>
                            </div>

                            @php
                            $properties = json_decode($ruleDetail->properties);
                            @endphp

                            <div class="form-group col-md-8">
                                <ul id="sortable" class="row">
                                    <li class="col-3"><input type="checkbox" id="item-category" name="items[]" @if(in_array('item_category', $properties)) checked @endif
                                            value="item_category">Item Category</li>
                                    <li class="col-3"><input type="checkbox" id="item-name" name="items[]" @if(in_array('item_name', $properties)) checked @endif
                                            value="item_name">Item Name</li>
                                    <li class="col-3"><input type="checkbox" id="city" name="items[]" @if(in_array('city', $properties)) checked @endif
                                            value="city">City</li>
                                    <li class="col-3"><input type="checkbox" id="locality" name="items[]" @if(in_array('locality', $properties)) checked @endif
                                            value="locality">Locality</li>
                                </ul>
                            </div>

                            <div class="row align-items-center">
                                <div class="form-group col-md-12">
                                    <label>URL Structure</label>
                                    <div><input type="text" id="url" name="url" value="" placeholder=""
                                            class="form-control" isrequired="isrequired"></div>
                                </div>

                            </div>
                            <hr>



                            <div class="form-group col-md-12">
                                <label>Description</label>
                                <textarea name="description" id="" placeholder="Enter Description" class="form-control" cols="30" rows="10">{{$ruleDetail->description}}</textarea>
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
