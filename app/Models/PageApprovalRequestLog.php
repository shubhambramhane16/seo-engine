<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageApprovalRequestLog extends Model
{
    protected $table = 'page_approval_request_logs';

    protected $fillable = [
        'request_id',
        'action_by',
        'action',
        'from_status',
        'to_status',
        'comments',
    ];

    public function request()
    {
        return $this->belongsTo(PageApprovalRequest::class, 'request_id', 'id');
    }

    public function actionBy()
    {
        return $this->hasOne(User::class, 'id', 'action_by');
    }
}
