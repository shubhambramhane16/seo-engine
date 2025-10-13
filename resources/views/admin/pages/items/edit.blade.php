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

    #city_row {
        height: 20%;
        margin-top: 24px;
        margin-left: 9px;
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
                                <input type="checkbox" name="priority" id="priority" onclick='addField()' value="1" @if ($details->priority_sequence) checked @endif> <label for="priority">Show on top</label>
                                <span class="form-group col-md-12 appendPriority">
                                    @if ($details->priority_sequence)
                                    <label>Priority Sequence Number</label><input class="form-control" type="tel" id="" name="prioritysequence" value="{{ $details->priority_sequence }}" placeholder="Priority sequence number" maxlength="3" required>
                                    @endif
                                </span>
                            </div>
                            <div class="form-group col-md-12">
                                <input type="checkbox" name="is_trending" id="is_trending" value="1" @if ($details->is_trending) checked @endif> <label for="is_trending">Is
                                    Trending</label>

                            </div>
                            <div class="form-group col-md-6">
                                <label>Test Name</label>
                                <input type="text" name="test_name" value="{{ $details->test_name }}" isrequired="required" class="form-control" placeholder="Enter Test Name">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Test Slug</label>
                                <input type="text" name="slug" value="{{ $details->slug }}" isrequired="required" class="form-control" placeholder="Enter Test Slug">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Test Alias Name</label>
                                <textarea name="test_alias_name" class="form-control" placeholder="eg. Philadelphia Quant PCR, Kinase Domain test">{{ $details->test_alias_name }}</textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Test Code</label>
                                <input type="text" name="test_code" value="{{ $details->test_code }}" isrequired="required" class="form-control" placeholder="Enter Test Code">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Test Type</label>
                                <input type="text" name="test_type" value="{{ $details->test_type }}" class="form-control" placeholder="Enter Test Type">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Lab Name</label>
                                <input type="text" name="lab_name" value="{{ $details->lab_name }}" isrequired="required" class="form-control" placeholder="Enter Lab Name">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Category</label>
                                @php
                                $addedCategories = [];
                                @endphp
                                @if ($details->categories)
                                @php
                                $addedCategories = json_decode($details->categories, true);
                                @endphp
                                @endif
                                <select class="form-control" name="category_id[]" id="category" multiple>
                                    @if ($categories)
                                    @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @if (in_array($category->id, $addedCategories)) selected @endif>
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
                                <input type="text" class="form-control" isrequired="required" name="billing_category" value="{{ $details->billing_category }}" placeholder="Enter Billing Category">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Department</label>
                                @php
                                $otherDepartments = $details->other_departments;
                                if (trim($otherDepartments)) {
                                $otherDepartments = explode(',', $otherDepartments);
                                } else {
                                $otherDepartments = [$details->department_id];
                                }
                                @endphp
                                <select class="form-control" name="department_id[]" id="department" isrequired="required" multiple>
                                    <!-- <option value="">Select Department</option> -->
                                    @if ($departments)
                                    @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" @if (in_array($department->id, $otherDepartments)) selected @endif>
                                        {{ $department->department_name }}
                                    </option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Recommendation</label>
                                <input type="text" name="recommendation" value="{{ $details->recommendation ? $details->recommendation : 'No Special Preparation Required' }}" isrequired="required" class="form-control" placeholder="Enter Recommendation">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Gender</label>
                                <select class="form-control" name="gender" id="gender">
                                    <option value="">Select Gender</option>
                                    <option value="both" {{ runTimeSelection('both', $details->gender) }}>Both
                                    </option>
                                    <option value="male" {{ runTimeSelection('male', $details->gender) }}>Male
                                    </option>
                                    <option value="female" {{ runTimeSelection('female', $details->gender) }}>Female
                                    </option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Age Group</label>
                                <select class="form-control" name="age_group" id="age_group">
                                    <option value="">Select Age Group</option>
                                    <option value="all" {{ runTimeSelection('all', $details->age_group) }}>All
                                    </option>
                                    <option value="5" {{ runTimeSelection('5', $details->age_group) }}>5yrs+
                                    </option>
                                    <option value="18" {{ runTimeSelection(18, $details->age_group) }}>18yrs+
                                    </option>
                                    <option value="45" {{ runTimeSelection(45, $details->age_group) }}>45yrs+
                                    </option>
                                    <option value="60" {{ runTimeSelection(60, $details->age_group) }}>60yrs+
                                    </option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Report TAT</label>
                                <input type="text" name="report_tat" value="{{ $details->report_tat }}" isrequired="required" class="form-control" placeholder="Enter Report TAT">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Technique</label>
                                <input type="text" name="technique" value="{{ $details->technique }}" class="form-control" isrequired="required" placeholder="Enter Technique">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Specimen</label>
                                <textarea class="form-control" id="" name="specimen" rows="10" cols="30" value="" placeholder="Enter Specimen">{{ $details->specimen }}</textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Temperature</label>
                                <input type="text" name="temperature" value="{{ $details->temperature }}" class="form-control" isrequired="required" placeholder="Enter Temperature">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Cut-off</label>
                                <input type="text" name="cut_off" value="{{ $details->cut_off }}" class="form-control" isrequired="required" placeholder="Enter Cut Off">
                            </div>

                            <div class="form-group col-md-6">
                                <label>Profile</label>
                                <input type="text" name="profile" value="{{ $details->profile }}" class="form-control" placeholder="Enter Profile">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Container</label>
                                <input type="text" name="container" value="{{ $details->container }}" class="form-control" placeholder="Enter Container">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Volume</label>
                                <input type="text" name="volume" value="{{ $details->volume }}" class="form-control" placeholder="Enter Volume">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Method</label>
                                <input type="text" name="method" value="{{ $details->method }}" class="form-control" placeholder="Enter Method">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Schedule</label>
                                <input type="text" name="schedule" value="{{ $details->schedule }}" class="form-control" isrequired="required" placeholder="Enter Schedule">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Run Days At Section</label>
                                <input type="text" name="run_days_at_section" value="{{ $details->run_days_at_section }}" class="form-control" placeholder="Enter Run Days At Section">
                            </div>

                            <div class="form-group col-md-12">
                                <label>Instructions</label>
                                <textarea class="form-control" id="instructions" name="instructions" rows="10" cols="30" value="" placeholder="Enter Instructions">{{ $details->instructions }}</textarea>
                            </div>




                            <div class="form-group col-md-12">
                                <label>Description</label>
                                <textarea class="form-control" id="editor1" name="description" value="{{ $details->description }}" rows="10" cols="30" value="" placeholder="Enter Description">{{ $details->description }}</textarea>
                            </div>



                            <div class="form-group col-md-12">
                                <label>Remarks</label>
                                <textarea class="form-control" id="" name="remarks" rows="10" cols="30" value="" placeholder="Enter Remarks">{{ $details->remarks }}</textarea>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Sample Remarks</label>
                                <textarea class="form-control" isrequired="required" name="sample_remarks" rows="10" cols="30" value="" placeholder="Enter Sample Remark">{{ $details->sample_remarks }}</textarea>
                            </div>

                            <div class="form-group col-md-12">
                                <label>Sample Type</label>
                                <input class="form-control" isrequired="required" name="sample_type" maxlength="100" placeholder="Enter sample type" value="{{ $details->sample_type }}">
                            </div>

                            <div class="form-group col-md-12">
                                <label>Sample Report</label>
                                <textarea class="form-control" id="" name="sample_report" rows="10" cols="30" value="" placeholder="Enter sample report">{{ $details->sample_report }}</textarea>
                            </div>
                            @php
                            if ($details->components) {
                            $componentsArr = json_decode($details->components, true);
                            $isComponents = true;
                            } else {
                            $componentsArr = [];
                            $isComponents = false;
                            }
                            @endphp
                            <div class="form-group col-md-9">
                                <label>Components</label>
                                @if (!$isComponents)
                                <input type="text" name="components[]" class="form-control" placeholder="">
                                @endif
                            </div>

                            <div class="form-group col-md-12" id="newRow2">
                                @if ($componentsArr && count($componentsArr) > 0)
                                @foreach ($componentsArr as $cKey => $component)
                                <div class="inputFormRow row">
                                    <div class="form-group col-md-9">
                                        <input class="form-control" isrequired="required" name="components[]" value="{{ $component['title'] }}" 1>
                                    </div>
                                    <div class="form-group col-md-2"><a href="javascript:void(0);" class="remove_button">
                                            <div class="btn btn-danger">-</div>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                                @else
                                @if ($componentsArr)
                                <div class="inputFormRow row">
                                    <div class="form-group col-md-9">
                                        <input class="form-control" isrequired="required" name="components[]" value="{{ $componentsArr }}" 2>
                                    </div>
                                    <div class="form-group col-md-2"><a href="javascript:void(0);" class="remove_button">
                                            <div class="btn btn-danger">-</div>
                                        </a>
                                    </div>
                                </div>
                                @endif
                                @endif
                            </div>

                            <div class="form-group col-md-10 text-right">
                                <div class="btn btn-primary" id="addRow2">+</div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>MRP</label>
                                <input type="text" name="mrp" isrequired="required" value="{{ $details->mrp }}" class="form-control number" placeholder="Enter MRP">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Selling Price</label>
                                <input type="text" name="selling_price" isrequired="required" value="{{ $details->selling_price }}" class="form-control number" placeholder="Enter Selling Price">
                            </div>
                            <div class="form-group col-md-12">
                                <label>Citywise Price</label>
                            </div>
                            <div class="form-group col-md-12">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                                    Add / Edit
                                </button>
                            </div>
                            <!-- The Modal -->
                            <div class="modal" id="myModal">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">

                                        <!-- Modal Header -->
                                        <div class="modal-header">
                                            <h4 class="modal-title">Citywise Price</h4>
                                            <button type="button" class="close fa-2x" data-dismiss="modal" aria-label="Close">
                                                <i aria-hidden="true" class="ki ki-close"></i>
                                            </button>
                                        </div>

                                        <!-- Modal body -->
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <input type="search" id="searchTest" class="form-control" placeholder="Search City">
                                            </div>

                                            <table class="table table-bordered">
                                                <tr>
                                                    <th>City</th>
                                                    <th>Locality</th>
                                                    <th><input type="checkbox" id="checkAll"> Availability</th>
                                                    <th>Price</th>

                                                </tr>
                                                @if (isset($cities) && count($cities) > 0)
                                                @foreach ($cities as $ckey => $city)
                                                <tr class="_city_id_{{ $city->id }} col_test_row" text-name="{{ $city->name }}">
                                                    <td style="width: 30%;">{{ $city->name }}</td>
                                                    <td style="width: 28%;">
                                                       <select class="form-control city_wise_locality" name="locality_id[{{ $city->id }}]">
                                                           <option value="">Locality</option>
                                                           @forelse(getLocality($city->id) as $key=>$val)
                                                                  <option value="{{$val->id}}" >{{$val->name}}</option>
                                                           @empty
                                                           @endforelse
                                                       </select>
                                                    </td>
                                                    <td><input type="checkbox" id="checkItem" class="city_wise_price_check" name="city_id[{{ $city->id }}]" value="{{ $city->id }}" /></td>
                                                    <td><input type="number" value="" name="city_wise_price[{{ $city->id }}]" class="form-control city_wise_price" placeholder="Enter Price" /></td>
                                                </tr>
                                                @endforeach
                                                @endif

                                            </table>
                                        </div>

                                        <!-- Modal footer -->
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success" data-bs-dismiss="modal">Update</button>

                                            <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                @php
                                if ($details->faqs_ids) {
                                $addedFaqs = json_decode($details->faqs_ids, 1);
                                } else {
                                $addedFaqs = [];
                                }
                                $faqs = getAllFaqs(1);
                                @endphp
                                <label>Select Faqs</label>
                                <select class="form-control" name="faqs_ids[]" id="faqs_ids" multiple>
                                    @if ($faqs)
                                    @foreach ($faqs as $faq)
                                    <option value="{{ $faq->id }}" @if (in_array($faq->id, $addedFaqs)) selected @endif>{{ $faq->title }}
                                    </option>
                                    @endforeach
                                    @endif
                                </select>
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
<link rel="stylesheet" href="{{ asset('multiselect/bootstrap.multiselect.css') }}" />
@endsection

{{-- Scripts Section --}}
@section('scripts')

<!-- <script src="//code.jquery.com/jquery-1.10.2.js"></script> -->
<!-- <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script> -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/js/bootstrap.bundle.min.js"></script> -->
<!-- <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script> -->
<script src="{{ asset('multiselect/bootstrap.multiselect.js') }}"></script>
<script src="https://ckeditor.com/docs/vendors/4.11.3/ckeditor/ckeditor.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function() {
        CKEDITOR.replace('editor1');
    });
</script>
<script>
    //All Check Checkbox
    $("#checkAll").click(function() {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });

    var addedCategories = `<?php if ($details->categories) {
                                echo $details->categories;
                            } ?>`;
    var addedSubCategories = `<?php if ($details->sub_categories) {
                                    echo $details->sub_categories;
                                } ?>`;
    var department_id = `<?php if ($details->department_id) {
                                echo $details->department_id;
                            } ?>`;
    var addedSpecialities = '<?php if ($details->specialities) {
                                    echo $details->specialities;
                                } ?>';
    var addedCitywisePrices = `<?php if ($details->citywise_prices) {
                                    echo $details->citywise_prices;
                                } ?>`;

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
        if (addedCategories) {
            var addedCategoriesArr = JSON.parse(addedCategories);
            if (addedCategoriesArr.length > 0) {
                getSubCategories(addedCategoriesArr);
            }
        }
        if (department_id) {
            getSpecialities(department_id);
        }
        if (addedCitywisePrices) {
            addedCitywisePricesArr = JSON.parse(addedCitywisePrices);
            if (addedCitywisePricesArr.length > 0) {
                $.each(addedCitywisePricesArr, function(index, value) {

                    if (value.city_id) {
                        if (value.availability == 1) {
                            $('._city_id_' + value.city_id).find('.city_wise_price_check').prop(
                                'checked', true);

                            $('._city_id_' + value.city_id).find('.city_wise_locality option').each(function(index,element){
                                let selectedEle =   element.value
                                if(selectedEle==value.locality_id){
                                    element.setAttribute('selected','true');
                                }

                            });
                        } else {
                            $('._city_id_' + value.city_id).find('.city_wise_price_check').prop(
                                'checked', false);
                        }
                        $('._city_id_' + value.city_id).find('.city_wise_price').val(value.city_price);

                    }

                })
            }
        }
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

    function addField() {
        var checkbox = document.getElementById('priority').checked;
        if (checkbox == true) {
            $('.appendPriority').append(
                '<label>Priority Sequence Number</label><input class="form-control" type="tel" id="" name="prioritysequence" value="" placeholder="Priority sequence number" maxlength="3" required>'
            );
        } else {
            $('.appendPriority input').remove();
            $('.appendPriority label').remove();
        }
    }
</script>

@endsection
