@extends('admin.layout.default')

@section('categorymaster','active menu-item-open')
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
                                <label>Category</label>
                                <select name="parent_id" id="" class="form-control" isrequired="required">
                                    <option value="">Select Category</option>
                                    @if($categories)
                                    @foreach($categories as $key => $list)
                                    <option value="{{$list->id}}" {{runTimeSelection($list->id,$parent_id)}}>{{$list->category_name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Sub Category Name</label>
                                <div><input type="text" name="category_name" class="form-control" placeholder="Enter Sub Category Name" isrequired="required"></div>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Short Description</label>
                                <div>
                                    <textarea name="category_short_description" class="form-control" placeholder="Write Short Description"></textarea>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Sub Category Icon <small>(Width: 96px, Height: 96px)</small></label>
                                <div><input type="file" name="icon" class="form-control"></div>
                            </div>
                            <div class="form-group col-md-12">
                                <center><button class="btn btn-success">Submit</button></center>
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