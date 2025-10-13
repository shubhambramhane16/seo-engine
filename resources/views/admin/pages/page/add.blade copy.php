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
                                    <label>Rule</label>
                                    @php
                                    $states = getAllStates();
                                    @endphp
                                    <select name="state_id" value="" class="form-control">
                                        <option value="">Select State</option>
                                        @if($states)
                                        @foreach($states as $key => $list)
                                        <option value="{{$list->id}}" {{runTimeSelection($list->id, request('state_id'))}}>{{$list->name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="dataTables_length">
                                    <label>Number of Combination</label>
                                    <h1>5000</h1>
                                </div>
                            </div>

                        </div>
                        <div class="row align-items-center mt-5">
                            <div class="form-group col-md-12">
                                <div class="text-center"><button class="btn btn-success">Generate</button></div>
                            </div>

                        </div>
                        <div class="row col-lg-12 pl-0 pr-0">
                            <div class="col-sm-12">
                                <div class="page-noted">
                                    <label><strong>Noted:</strong></label>
                                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsum consequuntur impedit quasi architecto reprehenderit maxime tenetur nisi, pariatur vel quod odit asperiores unde iure? Ipsa, cum! Accusantium corrupti sunt nihil pariatur ipsa.</p>
                                    <ul>
                                        <li>
                                            point 1
                                        </li>
                                        <li>
                                            point 2
                                        </li>
                                        <li>
                                            point 3
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
                                            <th class="custom_sno">SNo.</th>
                                            <th class="custom_sno">Rule Id</th>
                                            <th>Rule</th>
                                            <th class="custom_status">Status</th>
                                            <th class="custom_sno">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                                <!--end: Datatable-->
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
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
@endsection

{{-- Styles Section --}}
@section('styles')

<!-- <link href="//cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" /> -->
<link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection


