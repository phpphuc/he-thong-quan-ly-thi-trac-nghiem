<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';
    protected $primaryKey = 'questionid';

    protected $fillable = [
        'subjectid',
        'teacherid',
        'question',
        'level',
        'rightanswer',
        'answer_a',
        'answer_b',
        'answer_c',
        'answer_d',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacherid', 'Teacherid');
    }
}
