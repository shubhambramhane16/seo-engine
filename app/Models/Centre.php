<?php

namespace App\Models; 

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Centre extends Model
{ 
    use SoftDeletes;
    protected $table = 'centres';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'centre_name',
        'slug',
        'phone', 
        'landline', 
        'email', 
        'password', 
        'address_line1', 
        'address_line2', 
        'locality', 
        'landmark', 
        'country_id', 
        'state_name', 
        'state_id', 
        'city_name', 
        'city_id', 
        'pincode', 
        'centre_lat', 
        'centre_lng', 
        'centre_timings', 
        'centre_facilities', 
        'centre_images', 
        'head_name', 
        'head_mobile', 
        'head_email', 
        'contract_documents', 
        'seo_title', 
        'seo_description', 
        'seo_keywords', 
        'display_name', 
        'lead_flow', 
        'about_us', 
        'centre_type', 



        'created_by',
        'updated_by',
        'status',
        'deleted_at'
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

    // public function state()
    // {
    //     return $this->hasOne('App\Models\State', 'id','state_id');
    // }
    // public function cityDetails()
    // {
    //     return $this->hasOne('App\Models\CityDetails', 'city_id','id');
    // }
}
