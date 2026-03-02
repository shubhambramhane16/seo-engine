<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserApprovalHierarchy extends Model
{
    protected $table = 'user_approval_hierarchies';

    protected $fillable = [
        'user_id',
        'manager_id',
        'admin_id',
        'created_by',
        'updated_by',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function manager()
    {
        return $this->hasOne(User::class, 'id', 'manager_id');
    }

    public function admin()
    {
        return $this->hasOne(User::class, 'id', 'admin_id');
    }
}
