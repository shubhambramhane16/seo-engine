<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class OrderStatus extends Model
{
    // use SoftDeletes;
    protected $table = 'order_status';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status_title',
        'booking_type',
        'sequence', 
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
 
    public function orderStatus()
    {
        return $this->hasOne('App\Models\OrderStatus', 'id','order_status');
    }
    
    // public function SubCategory()
    // {
    //     return $this->hasMany('App\Models\Category', 'parent_id','id');
    // }
}
