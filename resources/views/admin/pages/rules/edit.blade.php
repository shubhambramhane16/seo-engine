@extends('admin.layout.default')

@section('rulemaster', 'active menu-item-open')
@section('content')


    <div class="card card-custom">

        <div class="card-body">
            <div class="mb-7">
                <div class="row align-items-center">

                    <form method="POST" action="" class="w-100">
                        {{ csrf_field() }}
                        <div class="col-lg-9 col-xl-12">
                            <div class="row">
                                <div class="form-group col-md-8">
                                        <label>Rule Name</label>
                                        <div>
                                            <input type="text" id="rule_name" name="rule_name" placeholder="Enter Rule Name"
                                                class="form-control" isrequired="isrequired" value="{{$ruleDetail->rule_name}}">
                                        </div>
                                    </div>
                                </div>
                            <div class="row align-items-center">
                                <div class="form-group col-md-4">
                                    <label>Prefix</label>
                                    <div>
                                        <input type="text" id="prefix" name="prefix" placeholder="Enter Prefix Name"
                                            class="form-control" value="{{$ruleDetail->prefix}}">
                                    </div>
                                </div>
                                @php
                                $properties = json_decode($ruleDetail->properties);
                                @endphp

                                <div class="form-group col-md-8">
                                    <ul id="sortable" class="row">
                                        <li class="col-3"><input type="checkbox" id="item-category" name="items[]" @if(in_array('category-name',$properties)) checked @endif
                                                value="category-name">Item Category</li>
                                        <li class="col-3"><input type="checkbox" id="item-name" name="items[]" @if(in_array('item-name',$properties)) checked @endif
                                                value="item-name">Item Name</li>
                                        <li class="col-3"><input type="checkbox" id="city-name" name="items[]" @if(in_array('city-name',$properties)) checked @endif
                                                value="city-name">City</li>
                                        <li class="col-3"><input type="checkbox" id="locality-name" name="items[]" @if(in_array('locality-name',$properties)) checked @endif
                                                value="locality-name">Locality</li>
                                    </ul>
                                </div>

                            </div>
                            <div class="row align-items-center">
                                <div class="form-group col-md-12">
                                    <label>URL Structure</label>
                                    @php
                                    if($ruleDetail->prefix && $properties)
                                    $url_structure = $ruleDetail->prefix . '/' . implode('/', $properties);
                                    else
                                    $url = '';
                                    @endphp
                                    <div><input type="text" id="url" name="url_structure" placeholder="" value="{{$url_structure}}"
                                            class="form-control" isrequired="isrequired"></div>
                                </div>

                            </div>
                            <hr>



                            <div class="row align-items-center">
                                <input type="hidden" name="template_id" id="template_id"  value="{{$ruleDetail->template_id}}">
                                <div class="form-group col-md-6">
                                    <div class="dataTables_length">
                                        <label>Template</label>
                                        <div class="temp-act-temp">
                                            <div class="row mt-2">

                                                @forelse($templates as $template)
                                                    <div class="col-md-3">
                                                        <div class="activated-temp" data-id="{{ $template->id }}">
                                                            <img src="{{Storage::disk('s3')->url($template->template_image)}}"
                                                                style="filter: grayscale(1);" alt="" />
                                                        </div>
                                                        <label for=""> {{ $template->template_name }}</label>
                                                    </div>



                                                @empty
                                                @endforelse


                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="row align-items-center">
                                <div class="form-group col-md-12">
                                    <label>Description</label>
                                    <textarea name="description" id="" placeholder="Enter Description" class="form-control" cols="30"
                                        rows="5">{{$ruleDetail->description}}</textarea>
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

@section('scripts')
    <script>
        $(function() {
            $("#sortable").sortable();
        });

        $(document).ready(function() {
            $("#prefix,#item-category,#item-name,#city,#locality").change(function() {
                var url = ($('#prefix').val());
                $('input[type=checkbox]').each(function(e) {
                    if ($(this).prop('checked')) {
                        url = url + '/' + $(this).val();
                    }
                });

                $('#url').val(url);
            });
        });
    </script>
    <script>

        // if template id is already set then add style="filter: grayscale(0); border: 1px solid #a6a4a4;" to selected template
        $(document).ready(function() {
            var template_id = $('#template_id').val();
            if (template_id != '') {
                $('.activated-temp').each(function() {
                    if ($(this).attr('data-id') == template_id) {
                        $(this).find('img').css('filter', 'grayscale(0)');
                        $(this).find('img').css('border', '1px solid #a6a4a4');
                    }
                });
            }
        });


        // select template and set template id and add style="filter: grayscale(0); border: 1px solid #a6a4a4;" to selected template
        $('.activated-temp').click(function() {
            $('.activated-temp').each(function() {
                $(this).find('img').css('filter', 'grayscale(1)');
                $(this).find('img').css('border', 'none');
            });
            $(this).find('img').css('filter', 'grayscale(0)');
            $(this).find('img').css('border', '1px solid #a6a4a4');
            $('#template_id').val($(this).attr('data-id'));
        });

    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
@endsection

{{-- Styles Section --}}
@section('styles')

@endsection

{{-- Scripts Section --}}
@section('scripts')
@endsection
