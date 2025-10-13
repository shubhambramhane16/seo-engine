@extends('admin.layout.default')

@section('packagemaster','active menu-item-open')
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
                                <input type="checkbox" name="priority" id="priority" onclick='addField()' value="1"> Show on top
                            </div>
                            <div class="form-group col-md-12 appendPriority"></div>
                            <div class="form-group col-md-6">
                                <label>Package Name </label>
                                <div><input type="text" name="package_name" value="{{old('package_name')}}" isrequired="required" class="form-control" placeholder="Enter Package Name"></div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Package Code </label>
                                <div><input type="text" name="package_code" value="{{old('package_code')}}" isrequired="required" class="form-control" placeholder="Enter Package Code"></div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Recommendation</label>
                                <div><input type="text" name="recommendation" value="{{old('recommendation') ? old('recommendation') : 'No special preparation required'}}" isrequired="required" class="form-control" placeholder="Enter Recommendation"></div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Sample Type</label>
                                <div><input type="text" name="sample_type" value="{{old('$details->sample_type')}}" isrequired="required" class="form-control" placeholder="Enter Sample Type"></div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Gender</label>
                                <select class="form-control" name="gender" id="gender">
                                    <option value="">Select Gender</option>
                                    <option value="both" {{runTimeSelection('both',old('gender'))}}>Both</option>
                                    <option value="male" {{runTimeSelection('male',old('gender'))}}>Male</option>
                                    <option value="female" {{runTimeSelection('female',old('gender'))}}>Female</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Age Group</label>
                                <select class="form-control" name="age_group" id="age_group">
                                    <option value="">Select Age Group</option>
                                    <option value="all" {{runTimeSelection('all',old('age_group'))}}>All</option>
                                    <option value="5" {{runTimeSelection('5',old('age_group'))}}>5yrs+</option>
                                    <option value="18" {{runTimeSelection(18,old('age_group'))}}>18yrs+</option>
                                    <option value="45" {{runTimeSelection(45,old('age_group'))}}>45yrs+</option>
                                    <option value="60" {{runTimeSelection(60,old('age_group'))}}>60yrs+</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Department</label>
                                <select class="form-control" name="department_id[]" id="department" isrequired="required" multiple>
                                    <!-- <option value="">Select Department</option> -->
                                    @if ($departments)
                                    @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" @if (old('department_id') && in_array($department->id, old('department_id'))) selected @endif>
                                        {{ $department->department_name }}
                                    </option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Technique</label>
                                <input type="text" name="technique" isrequired="required" value="{{ old('technique') }}" class="form-control" placeholder="Enter Technique">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Temperature</label>
                                <input type="text" name="temperature" isrequired="required" value="{{ old('temperature') }}" class="form-control" placeholder="Enter Temperature">
                            </div>

                            <div class="form-group col-md-12">
                                <label>Sample Remarks</label>
                                <textarea class="form-control" isrequired="required" name="sample_remarks" rows="10" cols="30" value="" placeholder="Enter Sample Remark">{{ old('sample_remarks') }}</textarea>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Billing Category</label>
                                <input type="text" class="form-control" isrequired="required" name="billing_category" value="{{ old('billing_category') }}" placeholder="Enter Billing Category">
                            </div>

                            <div class="form-group col-md-6">
                                <label>Schedule</label>
                                <input type="text" name="schedule" isrequired="required" class="form-control" placeholder="Enter Schedule">
                            </div>

                            <div class="form-group col-md-9">
                                <label>Components</label>
                                <input type="text" name="components[]" class="form-control" placeholder="">
                            </div>
                            <div class="form-group col-md-2 margin-top-25">
                                <div class="btn btn-primary" id="addRow2">+</div>
                            </div>
                            <div class="form-group col-md-12" id="newRow2"></div>



                            <div class="form-group col-md-6">
                                <label>MRP</label>
                                <div><input type="text" name="mrp" value="{{old('mrp')}}" isrequired="required" class="form-control number" placeholder="Enter MRP"></div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Selling Price</label>
                                <div><input type="text" name="selling_price" value="{{old('selling_price')}}" isrequired="required" class="form-control number" placeholder="Enter Selling Price"></div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Report TAT</label>
                                <div><input type="text" name="report_tat" value="{{old('report_tat')}}" isrequired="required" class="form-control" placeholder="Enter Report TAT"></div>
                            </div>


                            <div class="form-group col-md-12">
                                <label>Description</label>
                                <textarea class="form-control" id="" name="description" rows="10" cols="80" value=""></textarea>
                            </div>
                            <div class="form-group col-md-12">
                                @php

                                $subCategories = getSubCategories(1);
                                @endphp
                                <label>Select Categories <small>(multiple)</small></label>
                                <select class="form-control" name="sub_category_ids[]" id="sub_category_ids" multiple>
                                    @if( $subCategories)
                                    @foreach( $subCategories as $subCategory)
                                    <option value="{{$subCategory->id}}">{{$subCategory->category_name }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                @php

                                $faqs = getAllFaqs(1);
                                @endphp
                                <label>Select Faqs</label>
                                <select class="form-control" name="faqs_ids[]" id="faqs_ids" multiple>
                                    @if( $faqs)
                                    @foreach( $faqs as $faq)
                                    <option value="{{$faq->id}}">{{$faq->title}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Tests Count</label>
                                <div><input type="text" name="component_count" value="{{old('component_count')}}" class="form-control" placeholder="Eg. 10"></div>
                            </div>
                            @include('admin.layout.partials.extras.add-tests')

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
<style>
    .search-text {
        width: 200px;
    }
</style>
<link rel="stylesheet" href="{{ asset('multiselect/bootstrap.multiselect.css') }}" />
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script src="{{asset('multiselect/bootstrap.multiselect.js')}}"></script>
<script>
    $("#searchTest").on("keyup", function() {
        var val = $(this).val().toLowerCase();
        $(".col_test_row").filter(function() {
            $(this).toggle($(this).attr('text-name').toLowerCase().indexOf(val) > -1)
        });
        if (val.length >= 3) {

            $.ajax({
                type: 'POST',
                url: APP_URL + '/ajax/fetchtests?query=' + val + '&_token={{csrf_token()}}',
                dataType: 'json',

                success: function(response) {
                    // console.log(response);
                    var option = '';

                    if (addedTest) {
                        var addedTestArr = JSON.parse(addedTest);
                    } else {
                        var addedTestArr = [];
                    }
                    console.log(addedTestArr);
                    $.each(response.Result, function(index, value) {
                        selected = '';
                        console.log(value.test_id.toString());
                        if ($.inArray(value.test_id.toString(), addedTestArr) != '-1') {
                            selected = 'checked';
                        }
                        option += `
                        <div class="col-md-3 col_test_row" text-name="` + value.test_name + `">
                                <input type="checkbox" name="" class="form-check-input" id="_test_` + value.test_id + `" component='' value="` + value.test_id + `" testname="` + value.test_name + `" onchange="addRemoveTest(this)">
                                <label class="form-check-label" for="_test_` + value.test_id + `">` + value.test_name + `</label>
                        </div>
                        `;
                    })
                    $('.activeTests').html(option);

                }
            })
        }
    });

    function addField() {
        var checkbox = document.getElementById('priority').checked;
        if (checkbox == true) {
            $('.appendPriority').append('<label>Priority Sequence Number</label><input class="form-control" type="tel" id="" name="prioritysequence" value="" placeholder="Priority sequence number" maxlength="3" required>');
        } else {
            $('.appendPriority input').remove();
            $('.appendPriority label').remove();
        }
    }
    $(document).ready(function() {
        $('#faqs_ids').multiselect({
            nonSelectedText: 'Select Faqs',
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '100%'
        });

        $('#sub_category_ids').multiselect({
            nonSelectedText: 'Select Categories',
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '100%'
        });
    });
</script>

<script>

    


$('#department').multiselect({
            nonSelectedText: 'Select Department',
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '100%'
        });




        $("#addRow2").click(function() {
        var html = '';
        html +=
            '<div class="inputFormRow row"><div class="form-group col-md-9"><input class="form-control" id="" name="components[]"   isrequired="required"  value="" ></div><div class="form-group col-md-2"><a href="javascript:void(0);" class="remove_button"><div class="btn btn-danger">-</div></a></div></div>';
        $('#newRow2').append(html);
    });
    $(document).on('click', '.remove_button', function() {
        $(this).parent('div').parent('div').remove();
    });



    </script>



@endsection
