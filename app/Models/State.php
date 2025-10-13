<?php

namespace App\Models;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'states';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'country_id',
        'status',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_at',
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
