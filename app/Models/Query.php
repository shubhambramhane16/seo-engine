<?php

namespace App\Models;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Query extends Model
{
    use SoftDeletes;
    protected $table = 'queries';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_name',
        'customer_mobile',
        'customer_email',
        'message',
        'type',
        'city_id',
        'address',
        'prescriptions',
        'dob',
        'is_lead_converted',
        'gender',
        'centre_id',
        'page_source',
        'last_follow_up_date',
        'last_follow_up_comment',
        'next_follow_up_date',
        'is_sync_crm',
        'source_url',
        'query_nature',

        'status',
        'created_by',
        'updated_by',
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
    public function city()
    {
        return $this->hasOne('App\Models\City', 'id', 'city_id');
    }
    public function centre()
    {
        return $this->hasOne('App\Models\Centre', 'id', 'centre_id');
    }
    public function enquire_history()
    {
        return $this->hasMany('App\Models\EnquireHistory', 'query_id', 'id')->orderBy('id','desc');
    }
}
