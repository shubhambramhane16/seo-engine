@extends('admin.layout.default')

@section('userlist','active menu-item-open')
@section('content')
<div class="card card-custom">

    <div class="card-body">
        <div class="mb-7">
            <div class="row align-items-center">

                <form method="POST" action="" class="w-100">
                    {{ csrf_field() }}
                    <div class="col-lg-9 col-xl-12">
                        <div class="row align-items-start">
                            <div class="form-group col-md-6">
                                <label>Name</label>
                                <div><input type="text" name="name" value="{{$details->name}}" isrequired="required" class="form-control" placeholder="Enter User Name"></div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Email/User Name </label>
                                <div><input type="email" name="email" value="{{$details->email }}" isrequired="required" class="form-control" placeholder="Enter Email"></div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Mobile No.</label>
                                <div><input type="text" name="mobile" value="{{$details->mobile}}" isrequired="required" class="form-control" placeholder="Enter Mobile Number"></div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>User Role</label>

                                <select class="form-control" name="role_id" id="role_id" isrequired="required">
                                    <option value="">Select Role</option>
                                    @if($roles = getSystemRoles())
                                    @foreach($roles as $role )
                                    <option value="{{$role->id}}" {{runTimeSelection($details->role_id,$role->id)}}>{{ $role->role }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-12">
                                <div class="password-action-wrap">
                                    <button type="button" class="btn btn-light-primary font-weight-bold" data-toggle="modal" data-target="#changePasswordModal">
                                        Change Password
                                    </button>
                                    <span class="password-action-note">Optional: update only when needed</span>
                                </div>
                            </div>

                            @if(!empty($isSuperAdmin) && $isSuperAdmin)
                            <div class="form-group col-md-6">
                                <label>Reporting Manager</label>
                                <select class="form-control" name="reporting_manager_id">
                                    <option value="">Select Reporting Manager</option>
                                    @foreach($approvers as $approver)
                                    <option value="{{$approver->id}}" {{runTimeSelection(old('reporting_manager_id', optional($details->approvalHierarchy)->manager_id),$approver->id)}}>{{$approver->name}} ({{$approver->email}})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Final Approver (Admin)</label>
                                <select class="form-control" name="admin_approver_id">
                                    <option value="">Select Final Approver</option>
                                    @foreach($approvers as $approver)
                                    <option value="{{$approver->id}}" {{runTimeSelection(old('admin_approver_id', optional($details->approvalHierarchy)->admin_id),$approver->id)}}>{{$approver->name}} ({{$approver->email}})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label class="d-block">Page Generator Access</label>
                                <input type="hidden" name="can_access_page_generator" value="0">
                                <span class="switch switch-outline switch-icon switch-success">
                                    <label>
                                        <input type="checkbox" name="can_access_page_generator" value="1" {{ old('can_access_page_generator', optional($details->approvalHierarchy)->can_access_page_generator) ? 'checked' : '' }}>
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                            @endif
                            <div class="form-group col-md-12">
                                <center><button class="btn btn-success">Update</button></center>
                            </div>

                            <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body pb-2">
                                            <p class="text-muted mb-4">Leave both fields empty if you do not want to change password.</p>
                                            <div class="form-group">
                                                <label>New Password</label>
                                                <input type="password" name="password" class="form-control" placeholder="Enter New Password">
                                            </div>
                                            <div class="form-group mb-0">
                                                <label>Confirm New Password</label>
                                                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm New Password">
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-4">
                                            <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary" formaction="{{ url('admin/users/change-password/'.$details->id) }}" formmethod="POST">Save Password</button>
                                        </div>
                                    </div>
                                </div>
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
<style>
    .password-action-wrap {
        border: 1px dashed #cfd3e1;
        border-radius: 8px;
        padding: 14px 16px;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .password-action-note {
        color: #7e8299;
        font-size: 12px;
    }
</style>

@endsection

{{-- Scripts Section --}}
@section('scripts')
@endsection
