<?php

namespace App\Models;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $table = 'testimonials';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'city_id',
        'locality_id',
        'centre_id',
        'rating',
        'status',
        'created_at',
        'updated_at',

    ];

    public function city(){
       return $this->hasOne(City::class,'id','city_id')->select('id','name','slug');
    }
    public function locality(){
      return $this->hasOne(Locality::class,'id','locality_id')->select('id','name','slug');
    }
    public function centre(){
       return $this->hasOne(Centre::class,'id','centre_id')->select('id','centre_name');
    }
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
