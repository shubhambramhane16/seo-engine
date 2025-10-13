<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class LocalityImport extends Model
{
    protected $table = 'locality_import';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'country_id',
        'state_id',
        'city_id',
        'code',
        'slug',
        'created_by',
        'updated_by',
        'state_id',
        'status',
        'deleted_at',
        'description'
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

    public function city()
    {
        return $this->belongsTo('App\Models\City', 'city_id','id');
    }

    public function state()
    {
        return $this->belongsTo('App\Models\State', 'state_id','id');
    }


}
