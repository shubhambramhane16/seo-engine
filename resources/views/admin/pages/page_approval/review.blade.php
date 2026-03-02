@extends('admin.layout.default')

@section('pagemaster','active menu-item-open')
@section('content')
<div class="card card-custom">
    <div class="card-header flex-wrap border-0 pt-3 pb-0">
        <div class="card-title">
            <h3 class="card-label">Review Approval Request</h3>
        </div>
        <div class="card-toolbar">
            <a href="{{url('/admin/page/approval-requests')}}" class="btn btn-light-primary font-weight-bolder">
                <i class="la la-arrow-left"></i> Back to Queue
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="mb-6">
            <div><strong>Page:</strong> {{optional($requestDetails->page)->page_name}} ({{optional($requestDetails->page)->slug}})</div>
            <div><strong>Requested By:</strong> {{optional($requestDetails->requester)->name}}</div>
            <div><strong>Current Approver:</strong> {{optional($requestDetails->currentApprover)->name ?? '-'}}</div>
            <div><strong>Status:</strong> {{ucfirst(str_replace('_', ' ', $requestDetails->status))}}</div>
            @if($requestDetails->approver_comments)
            <div><strong>Latest Comments:</strong> {{$requestDetails->approver_comments}}</div>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 20%">Field</th>
                        <th style="width: 40%">Old (Live)</th>
                        <th style="width: 40%">New (Proposed)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($diffRows as $row)
                    <tr>
                        <td>{{$row['field']}}</td>
                        <td class="{{$row['changed'] ? 'bg-light-danger' : ''}}"><pre class="mb-0" style="white-space: pre-wrap">{{$row['old']}}</pre></td>
                        <td class="{{$row['changed'] ? 'bg-light-success' : ''}}"><pre class="mb-0" style="white-space: pre-wrap">{{$row['new']}}</pre></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(in_array($requestDetails->status, ['pending_manager', 'pending_admin']) && ($requestDetails->current_approver_id == auth()->user()->id || $isSuperAdmin))
        <div class="row mt-8">
            <div class="col-md-6">
                <form method="POST" action="{{url('/admin/page/approval-requests/'.$requestDetails->id.'/approve')}}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Approval Comments</label>
                        <textarea class="form-control" name="comments" rows="3" placeholder="Optional comments"></textarea>
                    </div>
                    <button class="btn btn-success" type="submit">Approve & Publish</button>
                </form>
            </div>
            <div class="col-md-6">
                <form method="POST" action="{{url('/admin/page/approval-requests/'.$requestDetails->id.'/reject')}}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Rejection Comments</label>
                        <textarea class="form-control" name="comments" rows="3" placeholder="Reason for rejection" required></textarea>
                    </div>
                    <button class="btn btn-danger" type="submit">Reject</button>
                </form>
            </div>
        </div>
        @endif

        <div class="mt-10">
            <h5>Audit Trail</h5>
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>When</th>
                        <th>Action</th>
                        <th>By</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requestDetails->logs as $log)
                    <tr>
                        <td>{{date('d M Y H:i', strtotime($log->created_at))}}</td>
                        <td>{{ucfirst(str_replace('_', ' ', $log->action))}}</td>
                        <td>{{optional($log->actionBy)->name}}</td>
                        <td>{{$log->from_status}}</td>
                        <td>{{$log->to_status}}</td>
                        <td>{{$log->comments}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
