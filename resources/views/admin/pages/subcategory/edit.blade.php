@extends('admin.layout.default')

@section('categorymaster','active menu-item-open')
@section('content')
<div class="card card-custom">
    <!-- <div class="card-header flex-wrap border-0 pt-6 pb-0">
        <div class="card-title">
            <h3 class="card-label">Edit Category
            </h3>
        </div>
    </div> -->
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
                                    <option value="{{$list->id}}" {{runTimeSelection($list->id,$details->parent_id)}}>{{$list->category_name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Sub Category Name</label>
                                <div><input type="text" name="category_name" value="{{$details->category_name}}" class="form-control" placeholder="Enter Sub Category Name" isrequired="required"></div>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Short Description</label>
                                <div>
                                    <textarea name="category_short_description" class="form-control" placeholder="Write Short Description">{{$details->category_short_description}}</textarea>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Sub Category Icon <small>(Width: 96px, Height: 96px)</small></label>
                                @if($details->category_icon)
                                <div class="_update_img_action">
                                    @if(str_contains($details->category_icon,'AWS'))
                                    <a target="_black" href="{{Storage::disk('s3')->url($details->category_icon)}}" class="btn btn-success btn-sm">View</a> &nbsp;
                                    @else
                                    <a target="_black" href="{{asset('uploads/category/icons/'.$details->category_icon)}}" class="btn btn-success btn-sm">View</a> &nbsp;
                                    @endif
                                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" onclick="updateImage()">Update</a>
                                    <br>
                                    <div class="_icon_display">
                                        @if(str_contains($details->category_icon,'AWS'))
                                        <img src="{{Storage::disk('s3')->url($details->category_icon)}}" />
                                        @else <img src="{{asset('uploads/category/icons/'.$details->category_icon)}}" />
                                        @endif
                                    </div>
                                </div>
                                @endif
                                <div class="image_file _update_img_file" style="{{$details->category_icon ? 'display:none' : ''}}">
                                    <input type="file" name="icon" class="form-control">
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <center><button class="btn btn-success">Update</button></center>
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