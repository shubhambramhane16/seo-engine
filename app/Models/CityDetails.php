<?php

namespace App\Models; 

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class CityDetails extends Model
{ 
    protected $table = 'city_details';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'city_id',
        'description'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

     
}
