<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;


    protected $fillable = [

        'name',
        'subject_id',
        'teacher_id',
        'time',
        'examtype',
        'Qtype1',
        'Qtype2',
        'Qtype3',
        'Qnumber',
    ];
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'teacher_id');
    }
    public function results()
    {
        return $this->hasMany(Result::class, 'exam_id', 'exam_id');
    }
    public function questions()
    {
        return $this->belongsToMany(Question::class, 'exam_question', 'exam_id', 'question_id');
    }
}
