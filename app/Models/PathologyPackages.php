<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PathologyPackages extends Model
{
    use SoftDeletes;
    protected $table = 'pathology_packages';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'package_name',
        'slug',
        'lab_name',
        'description',
        'component_count',
        'recommendation',
        'age_group',
        'mrp',
        'selling_price',
        'citywise_prices',
        'tests',
        'components',
        'report_tat',
        'state_id',
        'state_name',
        'show_ontop',
        'priority_sequence',
        'sample_type',
        'gender',
        'city_id',
        'city_name',
        'package_code',
        'banner',
        'faqs_ids',
        'sub_category_ids',
        'sample_report',

        'created_by',
        'updated_by',
        'status',
        'deleted_at',
        'temperature',


        'schedule',
        'components',
        'department_id',
        'billing_category',
        'sample_remarks',
        'temperature',
        'technique',
        'other_departments',


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

    public function department()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }
    public function departmentData()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }
    static public function getActiveItems($limit = null)
    {
        return self::select(
            'id as Id',
            'slug as Slug',
            'package_name as PackageName',
            'lab_name as LabName',
            'description as Description',
            'recommendation as Recommendation',
            'sample_type AS SampleType',
            'gender AS Gender',
            'age_group as AgeGroup',
            'mrp as MRP',
            'selling_price as SellingPrice',
            'tests AS Tests',
            'report_tat as ReportTat',
            'state_id as StateId',
            'state_name as StateName',
            'city_id as CityId',
            'city_name as CityName',
            'show_ontop as ShowOnTop',
            'priority_sequence as PrioritySequence',
            'status as Status',
            'banner as Banner',
        )->when($limit, function ($condition) use ($limit) {
            if ($limit) {
                $condition->limit($limit);
            }
        })->where('status', 1)->where('show_ontop', 1)->orderBy('priority_sequence', 'asc')->orderBy('show_ontop', 'desc')->get();
    }
}
