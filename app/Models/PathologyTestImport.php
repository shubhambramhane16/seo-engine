<?php

namespace App\Models; 

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PathologyTestImport extends Model
{ 
    use SoftDeletes;
    protected $table = 'pathology_tests_import';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'test_code',
        'test_name', 
        'slug', 
        'lab_name', 
        'component_count', 
        'recommendation', 
        'age_group', 
        'mrp', 
        'selling_price', 
        'citywise_prices', 
        'description', 
        'show_ontop', 
        'priority_sequence', 
        'components', 
        'report_tat', 
        'categories', 
        'sub_categories', 
        'department_id', 
        'other_departments', 
        'specialities', 
        'technique', 
        'specimen', 
        'temperature', 
        'cut_off', 
        'profile', 
        'container', 
        'volume', 
        'method', 
        'faqs_ids', 
        'schedule', 
        'instructions',  
        'remarks', 
        'gender', 
        'created_by',
        'updated_by',
        'status',
        'is_trending',
        'category',
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

    public function department()
    {
        return $this->hasOne('App\Models\Department', 'id','department_id');
    }
    public function department_data()
    {
        return $this->hasOne('App\Models\Department', 'id','department_id');
    }
    // public function cityDetails()
    // {
    //     return $this->hasOne('App\Models\CityDetails', 'city_id','id');
    // }
}
