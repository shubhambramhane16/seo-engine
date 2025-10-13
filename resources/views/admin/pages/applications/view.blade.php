@extends('admin.layout.default')

@section('applicationmaster','active menu-item-open')
@section('content')
<div class="card card-custom">

    <div class="card-body">
        <div class="mb-7">
            <div class="row align-items-center">

                <!-- <form method="POST" action="" class="w-100" enctype="multipart/form-data"> -->

                <div class="col-lg-9 col-xl-12">
                    <div class="row align-items-center">
                        <div class="form-group col-md-6">
                            <label>Application Status</label>
                            <select onchange="changeApplicationStatus(this)" class="form-control form-control-sm" data-url="{{url('admin/job-applications/update-status/'.$details->id.'/')}}">
                                @if($jobApplicationStatus = jobApplicationStatus())
                                @foreach($jobApplicationStatus as $jkey => $val)
                                <option value="{{$jkey}}" {{runTimeSelection($jkey , $details->application_status)}}>{{$val}}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Job Title</label>
                            <div><input type="text" name="job_title" value="{{$details->Job ? $details->Job->job_title : ''}}" class="form-control" placeholder="Enter Job Title" disabled readonly></div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Name</label>
                            <div><input type="text" name="experience" value="{{$details->name}}" class="form-control" disabled readonly></div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Mobile</label>
                            <div><input type="text" name="location" value="{{$details->mobile}}" class="form-control" disabled readonly></div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <div><input type="text" name="location" value="{{$details->email}}" class="form-control" disabled readonly></div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Experience</label>
                            <div><input type="text" name="experience" value="{{$details->experience}}" class="form-control" disabled readonly></div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Location</label>
                            <div><input type="text" name="location" value="{{$details->location}}" class="form-control" disabled readonly></div>
                        </div>

                        <div class="form-group col-md-12">
                            <label>Experience Details</label>
                            <textarea class="form-control" name="jd" id="" cols="30" rows="10" disabled readonly>{{$details->exp_details}}</textarea>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Other Details</label>
                            <textarea class="form-control" name="jd" id="" cols="30" rows="10" disabled readonly>{{$details->other_details}}</textarea>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Remark</label>
                            <textarea class="form-control" name="jd" id="" cols="30" rows="10" disabled readonly>{{$details->remark}}</textarea>
                        </div>
                    </div>
                </div>

                <!-- </form> -->
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
    function changeApplicationStatus(e) {
        if (confirm("Do you want to change status?")) {
            var url = $(e).attr('data-url');
            var val = $(e).val();
            if (url)
                location.href = url + '/'+ val;
        }
    }
</script>
@endsection