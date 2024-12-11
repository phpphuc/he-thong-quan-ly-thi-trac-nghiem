<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';
    protected $primaryKey = 'question_id';

    protected $fillable = [
        'question_id',
        'subject_id',
        'teacher_id',
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
        return $this->belongsTo(Teacher::class, 'teacher_id', 'teacher_id');
    }
}
