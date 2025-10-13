<?php

namespace App\Models;


use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{ 
    use SoftDeletes;
    protected $table = 'faqs';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'faq_category_id', 
        'status',
        'faq_type',
        'created_by',
        'updated_by',
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
    public function faq_category()
    {
        return $this->hasOne('App\Models\FaqCategory','id','faq_category_id');
    }
}
