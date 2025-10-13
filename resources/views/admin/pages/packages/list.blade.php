@extends('admin.layout.default')

@section('packagemaster','active menu-item-open')
@section('content')
<div class="card card-custom">
    <div class="card-header flex-wrap border-0 pt-3 pb-0">
        <div class="card-title">
            <h3 class="card-label">Packages List
                <!-- <div class="text-muted pt-2 font-size-sm">Datatable initialized from HTML table</div> -->
            </h3>
        </div>
        <div class="card-toolbar">
            @include('admin.layout.partials.filters.common-filter')
            <!--begin::Button-->
            <a href="{{url('/admin/packages/add')}}" class="btn btn-primary font-weight-bolder">
                <i class="la la-plus"></i>Add Package</a>
            <!--end::Button-->
        </div>
        <form action="" method="get" class="w-100">
            <div class="row col-lg-12  pl-0 pr-0"> 
                <div class="col-sm-3">
                    <div class="dataTables_length">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="0" @if(request('status')=='0' ) {{runTimeSelection(0, request('status'))}} @endif>InActive</option>
                            <option value="1" @if(request('status')=='1' ) {{runTimeSelection(1, request('status'))}} @endif>Active</option>
                        </select>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="dataTables_length">
                        <label cla>&#160; </label>
                        <button type="submit" class="btn btn-success  mt-7" data-toggle="tooltip" title="Apply Filter">Filter</button>
                        <a href="{{url('admin/packages/list')}}" type="reset" class="btn btn-default  mt-7" data-toggle="tooltip" title="Reset Filter">Reset</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body">
        <!--begin: Search Form-->
        <!--begin::Search Form-->
        <!--begin: Datatable-->
        <table class="table table-bordered table-hover" id="myTable">
            <thead>
                <tr>
                    <th class="custom_sno">SNo.</th>
                    <th>Package Name</th>
                    <th>Package Code</th> 
                    <th>Price</th>

                    <th class="custom_status">Status</th>
                    <th class="custom_action">Action</th>
                </tr>
            </thead>
            <tbody>
                @if(count($packages) > 0)
                @foreach($packages as $key => $value)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$value->package_name }}</td>
                    <td>{{$value->package_code ? $value->package_code : 'NA' }}</td> 
                    <td><span class="mrp_price" data-toggle="tooltip" title="MRP">₹{{$value->mrp }}</span> /&nbsp;<span class="selling_price" data-toggle="tooltip" title="Selling Price">₹{{$value->selling_price }}</span></td> 
                    <td>
                        <a href="javascript:void(0)" data-url="{{url('admin/packages/update-status/'.$value->id.'/'.$value->status)}}" onclick="changeStatus(this)"> <span class="label label-lg font-weight-bold label-light-{{($value->status == 1) ? 'success' : 'danger'}} label-inline">{{($value->status == 1) ? 'Active' : 'InActive'}}</span></a>
                    </td>
                    <td>
                        <a href="{{url('/admin/packages/edit/'.$value->id)}}" class="btn btn-sm btn-clean btn-icon" title="Edit details" data-toggle="tooltip">
                            <i class="la la-edit"></i>
                        </a>
                        <a href="javascript:void(0)" data-url="{{url('/admin/packages/delete/'.$value->id)}}" class="btn btn-sm btn-clean btn-icon" data-toggle="tooltip" title="Delete" onclick="deleteItem(this)">
                            <i class="la la-trash"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
                @endif


            </tbody>
        </table>
        <!--end: Datatable-->
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