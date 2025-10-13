<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'country_id',
        'code',
        'slug',
        'created_by',
        'updated_by',
        'state_id',
        'status',
        'local_schema_markup',
        'max_order_limit',
        'is_payment_active',
        'deleted_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    public function state()
    {
        return $this->hasOne('App\Models\State', 'id','state_id');
    }
    public function cityDetails()
    {
        return $this->hasOne('App\Models\CityDetails', 'city_id','id');
    }

    public function locality()
    {
        return $this->hasMany('App\Models\Locality', 'city_id','id');
    }
}
