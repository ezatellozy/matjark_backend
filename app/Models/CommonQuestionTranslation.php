<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommonQuestionTranslation extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];
    public $translationForeignKey = 'common_question_id';

}
