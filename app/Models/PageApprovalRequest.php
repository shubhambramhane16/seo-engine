<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageApprovalRequest extends Model
{
    protected $table = 'page_approval_requests';

    protected $fillable = [
        'page_id',
        'requested_by',
        'manager_approver_id',
        'admin_approver_id',
        'current_approver_id',
        'old_payload',
        'new_payload',
        'status',
        'approver_comments',
        'approved_by',
        'rejected_by',
        'overridden_by',
        'reviewed_at',
        'published_at',
    ];

    protected $casts = [
        'old_payload' => 'array',
        'new_payload' => 'array',
        'reviewed_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function page()
    {
        return $this->hasOne(Pages::class, 'id', 'page_id');
    }

    public function requester()
    {
        return $this->hasOne(User::class, 'id', 'requested_by');
    }

    public function currentApprover()
    {
        return $this->hasOne(User::class, 'id', 'current_approver_id');
    }

    public function managerApprover()
    {
        return $this->hasOne(User::class, 'id', 'manager_approver_id');
    }

    public function adminApprover()
    {
        return $this->hasOne(User::class, 'id', 'admin_approver_id');
    }

    public function approver()
    {
        return $this->hasOne(User::class, 'id', 'approved_by');
    }

    public function rejector()
    {
        return $this->hasOne(User::class, 'id', 'rejected_by');
    }

    public function overrideBy()
    {
        return $this->hasOne(User::class, 'id', 'overridden_by');
    }

    public function logs()
    {
        return $this->hasMany(PageApprovalRequestLog::class, 'request_id', 'id')->orderBy('id', 'desc');
    }
}
