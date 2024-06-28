<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Product;

class CommonQuestion extends Model
{
    use HasFactory, Translatable, SoftDeletes;
     
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    public $translatedAttributes = ['question', 'answer'];
    
    public function product(){
        return $this->belongsTo(Product::class);
    }

    
}