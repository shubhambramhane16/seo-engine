<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pages extends Model
{
    use SoftDeletes;
    protected $table = 'pages';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'rule_id',
        'page_name',
        'slug',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'status',
        'deleted_at',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'page_url',
        'og_meta_title',
        'og_meta_description',
        'og_meta_image_url',
        'twitter_card_title',
        'schema_markup',
        'header_content',
        'center_content',
        'footer_content',
        'page_script',
        'twitter_card_description',
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
