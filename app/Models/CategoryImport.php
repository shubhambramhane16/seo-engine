<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryImport extends Model
{
    use SoftDeletes;
    protected $table = 'categories_import';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'category_name',
        'category_slug',
        'category_icon',
        'category_heading',
        'category_short_description',
        'category_details',
        'show_on_home',
        'position',
        'sequence',
        'tags',
        'seo_title',
        'seo_description',
        'seo_kewords',
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
    public function category()
    {
        return $this->hasOne('App\Models\Category', 'id','parent_id');
    }
    public function SubCategory()
    {
        return $this->hasMany('App\Models\Category', 'parent_id','id');
    }
}
