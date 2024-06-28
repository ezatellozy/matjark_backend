<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class CategoryFeature extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];
   
}
