@extends('admin.layout.default')

@section('itemsmaster', 'active menu-item-open')
@section('content')

<style>
    .margin-top-25 {
        margin-top: 25px;
    }

    .padding-left-0 {
        padding-left: 0px;
    }
</style>
<div class="card card-custom">
    <div class="card-body">
        <div class="mb-7">
            <div class="row align-items-center">

                <form method="POST" action="" class="w-100" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="col-lg-12">
                        <div class="row align-items-center">
                            <div class="form-group col-md-12">
                                <input type="checkbox" name="priority" id="priority" onclick='addField()' value="1"> <label for="priority">Show on top</label>
                                <span class="form-group col-md-12 appendPriority"></span>
                            </div>

                            <div class="form-group col-md-12">
                                <input type="checkbox" name="is_trending" id="is_trending" value="1"> <label for="is_trending">Is Trending</label>

                            </div>
                            <div class="form-group col-md-6">
                                <label>Test Name</label>
                                <input type="text" name="test_name" value="{{ old('test_name') }}" isrequired="required" class="form-control" placeholder="Enter Test Name">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Test Alias Name</label>
                                <textarea type="text" name="test_alias_name" value="{{ old('test_alias_name') }}" class="form-control" placeholder="eg. Philadelphia Quant PCR, Kinase Domain test"></textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Test Code</label>
                                <input type="text" name="test_code" value="{{ old('test_code') }}" isrequired="required" class="form-control" placeholder="Enter Test Code">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Test Type</label>
                                <input type="text" name="test_type" value="{{ old('test_type') }}" class="form-control" placeholder="Enter Test Type">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Lab Name</label>
                                <input type="text" name="lab_name" value="{{ old('lab_name') }}" isrequired="required" class="form-control" placeholder="Enter Lab Name">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Category</label>
                                <select class="form-control" name="category_id[]" id="category" multiple>
                                    @if ($categories)
                                    @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ runTimeSelection($category->id, old('category_id')) }}>
                                        {{ $category->category_name }}
                                    </option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Sub Category</label>
                                <select class="form-control" name="sub_category_id[]" id="subcategory" multiple>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Billing Category</label>
                                <input type="text" class="form-control" isrequired="required" name="billing_category" value="{{ old('billing_category') }}" placeholder="Enter Billing Category">
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
                                <label>Recommendation</label>
                                <input type="text" name="recommendation" value="{{ old('recommendation') ? old('recommendation') : 'No Special Preparation Required.' }}" isrequired="required" class="form-control" placeholder="Enter Recommendation">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Gender</label>
                                <select class="form-control" name="gender" id="gender">
                                    <option value="">Select Gender</option>
                                    <option value="both" {{ runTimeSelection('both', old('gender')) }}>Both</option>
                                    <option value="male" {{ runTimeSelection('male', old('gender')) }}>Male</option>
                                    <option value="female" {{ runTimeSelection('female', old('gender')) }}>Female
                                    </option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Age Group</label>
                                <select class="form-control" name="age_group" id="age_group">
                                    <option value="">Select Age Group</option>
                                    <option value="all" {{ runTimeSelection('all', old('age_group')) }}>All</option>
                                    <option value="5" {{ runTimeSelection('5', old('age_group')) }}>5yrs+</option>
                                    <option value="18" {{ runTimeSelection(18, old('age_group')) }}>18yrs+</option>
                                    <option value="45" {{ runTimeSelection(45, old('age_group')) }}>45yrs+</option>
                                    <option value="60" {{ runTimeSelection(60, old('age_group')) }}>60yrs+</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Report TAT</label>
                                <input type="text" name="report_tat" value="{{ old('report_tat') }}" isrequired="required" class="form-control" placeholder="Enter Report TAT">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Technique</label>
                                <input type="text" name="technique" isrequired="required" value="{{ old('technique') }}" class="form-control" placeholder="Enter Technique">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Specimen</label>
                                <textarea class="form-control" id="" name="specimen" rows="10" cols="30" value="" placeholder="Enter Specimen">{{ old('specimen') }}</textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Temperature</label>
                                <input type="text" name="temperature" isrequired="required" value="{{ old('temperature') }}" class="form-control" placeholder="Enter Temperature">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Cut-off</label>
                                <input type="text" name="cut_off" isrequired="required" value="{{ old('cut_off') }}" class="form-control" placeholder="Enter Cut Off">
                            </div>

                            <div class="form-group col-md-6">
                                <label>Profile</label>
                                <input type="text" name="profile" class="form-control">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Container</label>
                                <input type="text" name="container" class="form-control" placeholder="Enter Container">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Volume</label>
                                <input type="text" name="volume" class="form-control" placeholder="Enter Volume">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Method</label>
                                <input type="text" name="method" class="form-control" placeholder="Enter Method">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Schedule</label>
                                <input type="text" name="schedule" isrequired="required" class="form-control" placeholder="Enter Schedule">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Run Days At Section</label>
                                <input type="text" name="run_days_at_section" value="{{ old('run_days_at_section') }}" class="form-control" placeholder="Enter Run Days At Section">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Instructions</label>
                                <textarea class="form-control" id="" name="instructions" rows="10" cols="30" value="" placeholder="Enter Instructions"></textarea>
                            </div>

                            <div class="form-group col-md-12">
                                <label>Description</label>
                                <textarea class="form-control" id="" name="description" value="{{ old('description') }}" rows="10" cols="30" value="" placeholder="Enter Description"></textarea>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Remarks</label>
                                <textarea class="form-control" id="" name="remarks" rows="10" cols="30" value="" placeholder="Enter Remarks">{{ old('remarks') }}</textarea>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Sample Remarks</label>
                                <textarea class="form-control" isrequired="required" name="sample_remarks" rows="10" cols="30" value="" placeholder="Enter Sample Remark">{{ old('sample_remarks') }}</textarea>
                            </div>

                            <div class="form-group col-md-12">
                                <label>Sample Type</label>
                                <input class="form-control" isrequired="required" name="sample_type" maxlength="100" placeholder="Enter sample type" value="{{ old('sample_type') }}">
                            </div>

                            <div class="form-group col-md-12">
                                <label>Sample Report</label>
                                <textarea class="form-control" id="" name="sample_report" rows="10" cols="30" value="" placeholder="Enter sample report">{{ old('sample_report') }}</textarea>
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
                                <input type="text" name="mrp" isrequired="required" value="{{ old('mrp') }}" class="form-control number" placeholder="Enter MRP">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Selling Price</label>
                                <input type="text" name="selling_price" isrequired="required" value="{{ old('selling_price') }}" class="form-control number" placeholder="Enter Selling Price">
                            </div>
                            <div class="form-group col-md-12">
                                @php

                                $faqs = getAllFaqs(1);
                                @endphp
                                <label>Select Faqs</label>
                                <select class="form-control" name="faqs_ids[]" id="faqs_ids" multiple>
                                    @if ($faqs)
                                    @foreach ($faqs as $faq)
                                    <option value="{{ $faq->id }}">{{ $faq->title }}</option>
                                    @endforeach
                                    @endif
                                </select>
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
<link rel="stylesheet" href="{{ asset('multiselect/bootstrap.multiselect.css') }}" />
@endsection

{{-- Scripts Section --}}
@section('scripts')

<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('multiselect/bootstrap.multiselect.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#category').multiselect({
            nonSelectedText: 'Select Category',
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '100%'
        });
        $('#faqs_ids').multiselect({
            nonSelectedText: 'Select Faqs',
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '100%'
        });
        $('#subcategory').multiselect({
            nonSelectedText: 'Select Sub Category',
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '100%'
        });

        $('#speciality').multiselect({
            nonSelectedText: 'Select Speciality',
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '100%'
        });


        $('#department').multiselect({
            nonSelectedText: 'Select Department',
            includeSelectAllOption: true,
            enableFiltering: true,
            enableCaseInsensitiveFiltering: true,
            buttonWidth: '100%'
        });

    });
</script>
<script>
    $("#addRow2").click(function() {
        var html = '';
        html +=
            '<div class="inputFormRow row"><div class="form-group col-md-9"><input class="form-control" id="" isrequired="required" name="components[]" value="" ></div><div class="form-group col-md-2"><a href="javascript:void(0);" class="remove_button"><div class="btn btn-danger">-</div></a></div></div>';
        $('#newRow2').append(html);
    });
    $(document).on('click', '.remove_button', function() {
        $(this).parent('div').parent('div').remove();
    });

    function addField() {
        var checkbox = document.getElementById('priority').checked;
        if (checkbox == true) {
            $('.appendPriority').append(
                '<br><label>Priority Sequence Number</label><input class="form-control" type="tel" id="" name="prioritysequence" value="" placeholder="Priority sequence number" maxlength="3" required>'
            );
        } else {
            $('.appendPriority').html('');
        }
    }
</script>
@endsection
