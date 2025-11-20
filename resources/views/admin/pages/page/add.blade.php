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
                        <div class="row col-lg-12 pl-0 pr-0">
                            <div class="col-sm-4">
                                <div class="dataTables_length">
                                    <label>Select City</label>
                                    <select name="city_id" class="form-control select" id='city_id' onchange="ruleCombinations($('#rule_id').val(), this.value);">
                                        <option value="">Select City</option>

                                        @if( $cities= App\Models\City::where('status',1)->get())
                                        @foreach($cities as $city)
                                        <option value="{{$city->id}}" @if(request('city_id')==$city->id ) {{runTimeSelection($city->id, request('city_id'))}} @endif>{{$city->name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="dataTables_length">
                                    <label>Rule Name</label>
                                    <select name="rule_id" class="form-control select" id='rule_id' onchange="ruleCombinations(this.value, $('#city_id').val());">
                                        <option value="">Select Rule</option>
                                        @if($rules)
                                        @foreach($rules as $rule)
                                        <option value="{{$rule->id}}" @if(request('rule_id')==$rule->id ) {{runTimeSelection($rule->id, request('rule_id'))}} @endif>{{$rule->rule_name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-sm-4">
                                <div class="dataTables_length">
                                    <input type="hidden" name="number_of_combination" class='number_of_combination'>
                                    <label>Number of combination on selected rule : </label> <span style="font-size: 18px;" class='number_of_combination'> </span>
                                </div>
                            </div>
                        </div>
                        <div class="row align-items-center mt-5">
                            <div class="form-group col-md-12">
                                <div class="text-center"><button class="btn btn-success">Generate</button></div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="col-lg-9 col-xl-12">
                    <div class="row col-lg-12 pl-0 pr-0">
                        <div class="col-sm-12">
                            <div class="page-noted">
                                <label><strong>Noted:</strong></label>
                                <p>Different Permutation and combination will be used to target the keywords.</p>
                                <ul>
                                    <li>
                                        Pathology Labs
                                    </li>
                                    <li>
                                        Diagnostic Centres
                                    </li>
                                    <li>
                                        Preventive Health Checkup
                                    </li>
                                    <li>
                                        Full Body Checkup
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="row col-lg-12 pl-0 pr-0">
                        <div class="card-body" style="padding: 15px;">
                            <label for=""><strong>History:</strong></label>
                            <!--begin: Datatable-->
                            <table class="table table-bordered table-hover" id="myTable">
                                <thead>
                                    <tr>
                                        <th class="custom_sno">User</th>
                                        <th class="custom_sno">Date & Time</th>
                                        <th>Rule</th>
                                        <th class="custom_status">No. of Pages</th>
                                        <th class="custom_sno">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pagesHistory as $value)
                                    <tr>
                                        <td>{{ $value->user?->name }}</td>
                                        <td>{{ $value->created_at }}</td>
                                        <td>{{ $value->rule?->rule_name }}</td>
                                        <td>{{ $value->no_of_pages }}</td>
                                        <td>

                                        </td>
                                    </tr>

                                    @endforeach


                                </tbody>
                            </table>
                            <!--end: Datatable-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Styles Section --}}
@section('styles')
<!-- <link href="//cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" /> -->
<link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection


{{-- Scripts Section --}}
@section('scripts')

<script>
    // rule change
    $('.select').select2();


    function ruleCombinations(ruleId, cityId) {
        $.ajax({
            url: "{{url('ajax/ruleCombinations')}}",
            type: "GET",
            data: {
                ruleId: ruleId,
                cityId: cityId,
            },
            success: function(data) {
                console.log(data);
                $('.number_of_combination').val(data);
                $('.number_of_combination').html(data);
            }
        });
    }
</script>



<script>
    $(document).ready(function() {
        $('#myTable').DataTable();
        $('.dataTables_filter label input[type=search]').addClass('form-control form-control-sm');
        $('.dataTables_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
    });
</script>
{{-- vendors --}}
<script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js" type="text/javascript"></script>
<!-- <script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script> -->

{{-- page scripts --}}
<!-- <script src="{{ asset('js/pages/crud/datatables/basic/basic.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/app.js') }}" type="text/javascript"></script> -->
@endsection
