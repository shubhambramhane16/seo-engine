<?php

namespace App\Models; 

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;


class Department extends Model
{ 
    // use SoftDeletes;
    protected $table = 'departments';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'department_name',
        'slug',
        'main_banner',
        'description',
        'instruments',
        'structure',
        'team_image',
        'brochures',
        'scientist_count',
        'technician_count',
        'staff_count',
        'short_description',
        'created_by',
        'updated_by',
        'status',
        'icon',
        'is_brochures_page',
        'is_department_page',
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

    // public function state()
    // {
    //     return $this->hasOne('App\Models\State', 'id','state_id');
    // }
    // public function cityDetails()
    // {
    //     return $this->hasOne('App\Models\CityDetails', 'city_id','id');
    // }
}
