<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class OrderHistory extends Model
{
    // use SoftDeletes;
    protected $table = 'order_history';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'order_status',
        'comments', 
        'updated_fields', 
        'updated_by', 
        'created_at', 
        'updated_at', 
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

     
}
