<?php

namespace App\Models;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $table = 'enquiries';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'number',
        'slot_date',
        'slot_time',
        'city',
        'locality',
        'item_id',
        'item_reference',
        'form',
        'query',
        'is_sync',
        'status',
        'created_at',
        'updated_at',

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

    // public function vendor_business()
    // {
    //     return $this->hasOne('App\Models\State', 'id', 'city_id');
    // }
}
