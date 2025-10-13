<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PagesHistory extends Model
{
    use SoftDeletes;
    protected $table = 'pages_history';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'no_of_pages',
        'rule_id',
        'created_by',
        'updated_by',
        'status',
        'deleted_at',
        'updated_at',
        'created_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    public function rule()
    {
        return $this->hasOne('App\Models\Rules', 'id','rule_id');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id','created_by');
    }

}
