<?php

namespace App\Models;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerEnquiry extends Model
{
    // use SoftDeletes;
    protected $table = 'partner_enquiry';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email_id',
        'mobile',
        'otp',
        'address',
        'pincode',
        'state_id',
        'city_id',
        'state_name',
        'city_name',
        'ownership',
        'business_profession',
        'association_with_lpl',
        'questions_data',
        'status',
        'created_by',
        'message', 
        'is_sync_crm', 
        'source_url', 
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
    // public function city()
    // {
    //     return $this->hasOne('App\Models\City', 'id', 'city_id');
    // }
    // public function centre()
    // {
    //     return $this->hasOne('App\Models\Centre', 'id', 'centre_id');
    // }
}
