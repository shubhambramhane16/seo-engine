@extends('admin.layout.default')

@section('templatesmaster','active menu-item-open')
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
                                <div><input type="text" name="template_name" placeholder="Enter Template Name" class="form-control" value="{{$templateDetail->template_name}}"></div>
                            </div>

                            {{-- image preview --}}

                            <div class="form-group col-md-12">
                               @if(isset($templateDetail->template_image) ) <img src="{{$templateDetail->template_image}}"
                                alt="" id="image_preview" width="100px" height="100px"> @endif
                            </div>

                            <div class="form-group col-md-12">
                                <label>Select Template</label>
                                <input type="file" name="file" placeholder="Enter Template" id='file' class="form-control" value="{{$templateDetail->template_image}}">
                            </div>

                            <div class="form-group col-md-12">
                                <label>Description</label>
                                <textarea name="description" id="" placeholder="Enter Description" class="form-control" cols="30"
                                    rows="5">{{$templateDetail->description}}</textarea>
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
<script>
    // image preview show

    $('#file').change(function() {
        let reader = new FileReader();
        reader.onload = (e) => {
            $('#image_preview').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
    });


</script>


@endsection
