<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;


class Role extends Model
{
    // use SoftDeletes;
    protected $table = 'admin_user_role';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role',
        'additional_info',
        'permission', 
        'created_by',
        'updated_by',
        'status',
        'deleted_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    // public function customer()
    // {
    //     return $this->hasOne('App\Models\Customer', 'id', 'customer_id');
    // }
}
