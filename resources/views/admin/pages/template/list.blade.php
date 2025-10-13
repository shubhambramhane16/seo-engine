@extends('admin.layout.default')

@section('templatesmaster', 'active menu-item-open')
@section('content')

    <div class="card card-custom">

        <div class="card-header flex-wrap border-0 pt-3 pb-0">
            <div class="card-title">
                <h3 class="card-label">Template List
                </h3>
            </div>
            <div class="card-toolbar">
                {{-- @include('admin.layout.partials.filters.common-filter') --}}
                <!--begin::Button-->
                <a href="{{ url('/admin/templates/add') }}" class="btn btn-primary font-weight-bolder">
                    <i class="la la-plus"></i>Add Template</a>
                <!--end::Button-->
            </div>
            <form action="" method="get" class="w-100">
                <div class="row col-lg-12 pl-0 pr-0">


                    <div class="col-sm-3">
                        <div class="dataTables_length">
                            <label>Status</label>
                            <select name="status" value="" class="form-control">
                                <option value="-1">All Status</option>
                                <option value="0"
                                    @if (request('status') == '0') {{ runTimeSelection(0, request('status')) }} @endif>
                                    InActive</option>
                                <option value="1"
                                    @if (request('status') == '1') {{ runTimeSelection(1, request('status')) }} @endif>
                                    Active</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-5">
                        <div class="dataTables_length">
                            <label cla>&#160; </label>
                            <button type="submit" class="btn btn-success" data-toggle="tooltip" title="Apply Filter"
                                style="margin-top: 20px;">Filter</button>
                            <a href="{{ url('admin/templates/list') }}" class="btn btn-default" data-toggle="tooltip"
                                title="Reset Filter" style="margin-top: 20px;">Reset</a>
                        </div>
                    </div>

                </div>
            </form>
        </div>
        <div class="row p-5">


            @forelse ($templates as $value)
                <div class="col-md-2">
                    <div class="temp-act-inact-btn">
                        <div class="temp-active">
                            <a href="javascript:void(0)" data-url="{{url('admin/templates/update-status/'.$value->id.'/'.$value->status)}}" onclick="changeStatus(this)"> <span class="label label-lg font-weight-bold label-light-{{($value->status == 1) ? 'success' : 'danger'}} label-inline">{{($value->status == 1) ? 'Active' : 'InActive'}}</span></a>
                        </div>

                    <div class="temp-edit">
                        <a href="{{url('admin/templates/edit/'.$value->id)}}"><i class="fa fa-edit"></i></a>
                    </div>

                    </div>
                    @php
                        $image = $value->template_image;
                        if (empty($image)) {
                            $image = 'no-image.png';
                        }
                    @endphp
                    <div class="temp-img">
                        <img src="{{$image}}" alt="">
                    </div>

                    <div class="temp-name">
                        <h4>{{$value->template_name}}</h4>
                    </div>

                </div>

            @empty

            {{-- no data found --}}




            @endforelse


        </div>
        <hr>

    </div>


@endsection

{{-- Styles Section --}}
@section('styles')
    <!-- <link href="//cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" /> -->
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
    <script src="//cdn.datatables.net/1.11.2/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <!-- <script src="{{ asset('plugins/custom/datatables/datatables.bundle.js') }}" type="text/javascript"></script> -->

    {{-- page scripts --}}
    <!-- <script src="{{ asset('js/pages/crud/datatables/basic/basic.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/app.js') }}" type="text/javascript"></script> -->
@endsection
