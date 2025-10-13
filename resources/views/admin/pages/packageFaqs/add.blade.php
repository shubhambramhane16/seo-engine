@extends('admin.layout.default')

@section('packagefaqmaster','active menu-item-open')
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
                                <label>Faqs Title</label>
                                <div><input type="text" name="title" value="{{old('title')}}" class="form-control" placeholder="Enter title" isrequired="required"></div>
                            </div>
                            
                            <div class="form-group col-md-12">
                                <label>Faqs Description</label>
                                <div>
                                    <textarea id="textEditor" name="description" value="{{old('description')}}" class="form-control" placeholder="Enter Description" isrequired="required"></textarea>
                                </div>
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
<script src="https://ckeditor.com/docs/vendors/4.11.3/ckeditor/ckeditor.js" type="text/javascript"></script>
<script>
 $(function() {
        CKEDITOR.replace('textEditor');
 });
</script>

@endsection