<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    // use SoftDeletes;
    protected $table = 'countries';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_name',
        'code',
        'country_short_name',
        'flag',
        'slug',
        'description', 
        'created_by',
        'updated_by',
        'status', 
        'updated_at',
        'created_at'
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

    // public function state()
    // {
    //     return $this->hasOne('App\Models\State', 'id','state_id');
    // }
    // public function category()
    // {
    //     return $this->hasOne('App\Models\Category', 'id','parent_id');
    // }
    // public function SubCategory()
    // {
    //     return $this->hasMany('App\Models\Category', 'parent_id','id');
    // }
}
