@extends('admin.layout.default')

@section('applicationmaster','active menu-item-open')
@section('content')
<div class="card card-custom">
    <div class="card-header flex-wrap border-0 pt-3 pb-0">
        <div class="card-title">
            <h3 class="card-label">Job Applications</h3>
        </div>
        <div class="card-toolbar">
            @include('admin.layout.partials.filters.common-filter') 
        </div>
        <form action="" method="get" class="w-100">
            <div class="row col-lg-12 pl-0 pr-0">
                <div class="col-sm-3">
                    <div class="dataTables_length">
                        <label>Job Profile</label>
                        <select name="job_id" class="form-control">
                            <option value="">All Job Profile</option>
                            @if($jobs)
                            @foreach($jobs as $key => $job)
                            <option value="{{$job->id}}">{{$job->job_title}}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="dataTables_length">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">Status</option>
                            @if($jobApplicationStatus = jobApplicationStatus())
                            @foreach($jobApplicationStatus as $jkey => $val)
                            <option value="{{$jkey}}">{{$val}}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="dataTables_length">
                        <label>Date Range</label>
                        <input type="text" name="fromtodate" id="fromtodate" class="form-control input-sm w-100" placeholder="Date Range" autocomplete="off" value="{{request('fromtodate')}}" readonly>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="dataTables_length">
                        <label cla>&#160; </label>
                        <button type="submit" class="btn btn-success mt-7" data-toggle="tooltip" title="Apply Filter">Filter</button>

                        <label cla>&#160; </label>
                        <a href="{{url('admin/job-applications/list')}}" class="btn btn-default mt-7" data-toggle="tooltip" title="Reset Filter">Reset</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover" id="myTable">
            <thead>
                <tr>
                    <th class="custom_sno">SNo.</th>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Experience</th>
                    <th>Job Profile</th>
                    <th>Applied Date</th>
                    <th>CV</th>
                    <th>Status</th>
                    <th class="custom_action">Action</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($lists) && count($lists) > 0)
                @foreach($lists as $key => $value)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$value->name }}</td>
                    <td>{{$value->mobile }}</td>
                    <td>{{$value->experience }}</td>
                    <td>{{$value->job ? $value->job->job_title : '' }} <br> 
                    <strong>Role:</strong> {{$value->job ? $value->job->role : '' }}</td>
                    <td>{{displayDateTime($value->created_at) }}</td>
                    <td>
                        @if($value->resume)
                        <a href="{{$value->resume}}" target="_blank">
                            View CV
                        </a>
                        @else
                        NA
                        @endif
                    </td>
                    <td>
                        <select onchange="changeApplicationStatus(this)" class="form-control form-control-sm" data-url="{{url('admin/job-applications/update-status/'.$value->id.'/')}}">
                            @if($jobApplicationStatus = jobApplicationStatus())
                            @foreach($jobApplicationStatus as $jkey => $val)
                            <option value="{{$jkey}}" {{runTimeSelection($jkey , $value->application_status)}}>{{$val}}</option>
                            @endforeach
                            @endif
                        </select>
                    </td>
                    <td>
                        <a href="{{url('admin/job-applications/details/'.$value->id)}}">
                            <i class="la la-edit"></i>
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
<script>

</script>
@endsection

{{-- Styles Section --}}
@section('styles')
<!-- <link href="//cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" /> -->
<link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection


{{-- Scripts Section --}}
@section('scripts')
<script>
    function changeApplicationStatus(e) {
        if (confirm("Do you want to change status?")) {
            var url = $(e).attr('data-url');
            var val = $(e).val();
            if (url)
                location.href = url + '/'+ val;
        }
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