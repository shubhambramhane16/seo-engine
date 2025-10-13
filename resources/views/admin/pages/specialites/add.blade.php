@extends('admin.layout.default')

@section('specialitymaster','active menu-item-open')
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
                                <label>Department</label>
                                <select class="form-control" name="department_id" id="" isrequired="required">
                                    <option value="">Select Department</option>
                                    @if(isset($departments) && $departments)
                                    @foreach($departments as $key => $list)
                                    <option value="{{$list->id}}"  {{runTimeSelection($list->id,old('department_id'))}}>{{$list->department_name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Speciality Name</label>
                                <div><input type="text" name="speciality_name" value="{{old('speciality_name')}}" class="form-control" placeholder="Enter Speciality Name" isrequired="required"></div>
                            </div>
                            <!-- <div class="form-group col-md-12">
                                <label>Speciality Slug</label>
                                <div><input type="text" name="slug" class="form-control" placeholder="Enter Speciality Slug "></div>
                            </div> -->
                            <div class="form-group col-md-12">
                                <center><button class="btn btn-success">Submit</button></center>
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
@endsection