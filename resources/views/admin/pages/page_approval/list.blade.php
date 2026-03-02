@extends('admin.layout.default')

@section('pagemaster','active menu-item-open')
@section('content')
<div class="card card-custom">
    <div class="card-header flex-wrap border-0 pt-3 pb-0">
        <div class="card-title">
            <h3 class="card-label">Approval Requests</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{url('/admin/page/list')}}" class="btn btn-light-primary font-weight-bolder">
                <i class="la la-arrow-left"></i> Back to Pages
            </a>
        </div>
    </div>

    <div class="card-body">
        <table class="table table-bordered table-hover" id="approvalTable">
            <thead>
                <tr>
                    <th>SNo.</th>
                    <th>Page</th>
                    <th>Requested By</th>
                    <th>Current Approver</th>
                    <th>Status</th>
                    <th>Requested On</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $item)
                @php
                    $statusClass = 'secondary';
                    $statusLabel = ucfirst(str_replace('_', ' ', $item->status));
                    if($item->status == 'pending_manager' || $item->status == 'pending_admin') {
                        $statusClass = 'warning';
                        $statusLabel = 'Pending Approval';
                    } elseif($item->status == 'approved') {
                        $statusClass = 'success';
                    } elseif($item->status == 'rejected') {
                        $statusClass = 'danger';
                    }
                @endphp
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{optional($item->page)->page_name}}<br><small>{{optional($item->page)->slug}}</small></td>
                    <td>{{optional($item->requester)->name}}</td>
                    <td>{{optional($item->currentApprover)->name ?? '-'}}</td>
                    <td><span class="label label-lg font-weight-bold label-light-{{$statusClass}} label-inline">{{$statusLabel}}</span></td>
                    <td>{{date('d M Y H:i', strtotime($item->created_at))}}</td>
                    <td>
                        <a href="{{url('/admin/page/approval-requests/'.$item->id)}}" class="btn btn-sm btn-clean btn-icon" title="Review Request" data-toggle="tooltip">
                            <i class="la la-search"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('styles')
<link href="{{ asset('plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('scripts')
<script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $('#approvalTable').DataTable();
        $('.dataTables_filter label input[type=search]').addClass('form-control form-control-sm');
        $('.dataTables_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
    });
</script>
@endsection
