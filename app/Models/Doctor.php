<?php

namespace App\Models; 

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Doctor extends Model
{ 
    use SoftDeletes;
    protected $table = 'doctors';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'doctor_code',
        'name', 
        'email', 
        'mobile', 
        'password', 
        'otp', 
        'dob', 
        'gender', 
        'department_id', 
        'qualification', 
        'area_of_interest', 
        'expertise', 
        'details',
        'research_publication', 
        'awards', 
        'main_video', 
        'other_videos', 
        'profile_image', 
        'main_video_youtube_link', 
        'city_id', 
        'city_name', 
        'created_by',
        'updated_by',
        'status',
        'designation',
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

    public function department()
    {
        return $this->hasOne('App\Models\Department', 'id','department_id');
    }
    // public function cityDetails()
    // {
    //     return $this->hasOne('App\Models\CityDetails', 'city_id','id');
    // }
}
