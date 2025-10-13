<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;


class Rules extends Model
{
    // use SoftDeletes;
    protected $table = 'rules';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'prefix',
        'properties',
        'rule_name',
        'url_structure',
        'template_id',
        'status',
        'description',
        'created_by',
        'updated_by',
    ];

    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id' , 'id');
    }
}


