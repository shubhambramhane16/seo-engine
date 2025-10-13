@extends('admin.layout.default')

@section('pagemaster','active menu-item-open')
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
                                <label>Page Url</label>
                                <div><input type="text" name="page_url" placeholder="Enter Page Url" class="form-control" value="{{$details->page_url}}" ></div>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Reference Name</label>
                                <div><input type="text" name="page_name" placeholder="Enter Reference Name" class="form-control" value="{{$details->page_name}}" ></div>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Slug</label>
                                <div><input type="text" name="slug" placeholder="Enter Slug" class="form-control" value="{{$details->slug}}"  readonly></div>
                            </div>


                            <div class="form-group col-md-6">
                                <label>Title</label>
                                <div><input type="text" name="seo_title" placeholder="Enter Title" class="form-control" value="{{$details->seo_title}}" ></div>
                            </div>

                            <div class="form-group col-md-12">
                                <label>Meta Description</label>

                                <textarea class="form-control" name="seo_description">{{$details->seo_description}}</textarea>
                            </div>

                            <div class="form-group col-md-12">
                                <label>Meta Keywords</label>

                                <textarea class="form-control" name="seo_keywords">{{$details->seo_keywords}}</textarea>
                            </div>

                            <div class="form-group col-md-6">
                                <label>OG Meta Title</label>
                                <div><input type="text" name="og_meta_title" placeholder="Enter OG Meta Title" class="form-control" value="{{$details->og_meta_title}}" ></div>
                            </div>

                            <div class="form-group col-md-6">
                                <label>OG Meta Description</label>

                                <textarea class="form-control" name="og_meta_description">{{$details->og_meta_description}}</textarea>
                            </div>

                            <div class="form-group col-md-6">
                                <label>OG Meta Image Url</label>
                                <div><input type="text" name="og_meta_image_url" placeholder="Enter OG Meta Image Url" class="form-control" value="{{$details->og_meta_image_url}}" ></div>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Twitter card Title </label>
                                <div><input type="text" name="twitter_card_title" placeholder="Enter Twitter card Title" class="form-control" value="{{$details->twitter_card_title}}" ></div>
                            </div>

                            <div class="form-group col-md-12">
                                <label>Twitter card Description</label>
                                <textarea class="form-control" name="twitter_card_description">{{$details->twitter_card_description}}</textarea>
                            </div>

                            <div class="form-group col-md-12">
                                <label>Schema Markup</label>

                                <textarea class="form-control" name="schema_markup">{{$details->schema_markup}}</textarea>

                            </div>

                            <div class="form-group col-md-12">
                                <label>Header Content</label>

                                <textarea id="textEditor" class="form-control textEditor" name="header_content">{{$details->header_content}}</textarea>

                            </div>

                            <div class="form-group col-md-12">
                                <label>Center Content</label>

                                <textarea id="textEditor2" class="form-control textEditor" name="center_content">{{$details->center_content}}</textarea>


                            </div>

                            <div class="form-group col-md-12">
                                <label>Footer Content</label>
                                <textarea id="textEditor3" class="form-control textEditor" name="footer_content">{{$details->footer_content}}</textarea>


                            </div>

                            <div class="form-group col-md-12">
                                <label>Page Script</label>
                                <textarea class="form-control" name="page_script">{{$details->page_script}}</textarea>
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
<script src="https://ckeditor.com/docs/vendors/4.11.3/ckeditor/ckeditor.js" type="text/javascript"></script>
<script>
 $(function() {
        CKEDITOR.replace('textEditor');
        CKEDITOR.replace('textEditor2');
        CKEDITOR.replace('textEditor3');

        //
 });

</script>
@endsection
