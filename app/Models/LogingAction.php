<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;


class LogingAction extends  Model implements TranslatableContract
{
    use Translatable;
    protected $guarded = ['id', 'created_at', 'updated_at'];
    public $translatedAttributes = ['title'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}