<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'student_id',
        'score',
        'start_time',
        'exam_subject_id',
        'note',
    ];
    protected $dates = [
        'start_time',  
    ];
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    public function examSubject()
    {
        return $this->belongsTo('App\Models\ExamSubject', 'exam_subject_id', 'id');
    }
}
