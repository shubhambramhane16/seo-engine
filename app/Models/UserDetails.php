<?php

namespace App\Models;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model
{
    protected $table = 'admin_user_detail';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'image',
        'city',
        'language',
        'dob',
        'gender',
        'address',
        'city_id',
        'state_id',
        'pincode',
        'location',
        'profile_image',
        'device_token',
        'current_lat_lng',
        'current_city',
        'updated_by',
        'created_by',
      
       
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
  
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    

    
}
