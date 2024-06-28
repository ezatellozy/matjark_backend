<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaticPageMetaDataTranslation extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    protected $table = 'static_pages_meta_data_translations';
}