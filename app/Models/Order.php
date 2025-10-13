<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    // use SoftDeletes;
    protected $table = 'orders';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'booking_type',
        'order_type',
        'schedule_date',
        'schedule_time',
        'centre_id',
        'customer_id',
        'patient_id',
        'patient_firstname',
        'patient_lastname',
        'patient_number',
        'gender',
        'address',
        'address_id',
        'city_id',
        'prescription',
        'prescription_status',
        'prescription_comments',
        'customer_remark',
        'actual_order_details',
        'updated_order_details',
        'order_items_total',
        'hc_charges',
        'order_discount',
        'order_total',
        'advance_paid',
        'payment_status',
        'payment_type',
        'payment_mode',
        'payment_txn_id',
        'is_offline',
        'order_status',
        'cancel_reason',
        'next_follow_up_date',
        'last_follow_up_date',
        'last_follow_up_comment',
        'medium',

        'address_tag',
        'state_id',
        'pincode',
        'locality',
        'patient_relation',
        'patient_email',
        'patient_dob',
        'patient_age',
        'customer_firstname',
        'customer_lastname',
        'is_sync_crm',
        'source_url',
        'crm_booking_id',
        'crm_pid',
        'salutation',
        'house_no',
        'crm_state_id',
        'crm_city_id',
        'refund_initiate_id',
        'associate_booking_id',

        'created_by',
        'updated_by',
        'status',
        'deleted_at',
        'updated_at',
        'created_at'
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
    public function state()
    {
        return $this->hasOne('App\Models\State', 'id', 'state_id');
    }

    public function customer()
    {
        return $this->hasOne('App\Models\Customer', 'id', 'customer_id');
    }
    public function patient()
    {
        return $this->hasOne('App\Models\Patient', 'id', 'patient_id');
    }
    public function orderStatus()
    {
        return $this->hasOne('App\Models\OrderStatus', 'id', 'order_status');
    }

    public function orderHistory()
    {
        return $this->hasMany('App\Models\OrderHistory', 'order_id', 'id')->orderBy('id', 'desc');
    }
    public function customerAddress()
    {
        return $this->hasOne('App\Models\Address', 'id', 'address_id');
    }
    public function centre()
    {
        return $this->hasOne('App\Models\Centre', 'id', 'centre_id');
    }
    
    public function Transactions()
    {
        return $this->hasOne('App\Models\PaymentTransaction', 'order_id', 'id');
    }
}
