<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $table = 'exams'; 
    protected $primaryKey = 'exam_id'; 

    protected $fillable = [
        'exam_id',
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
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'teacher_id');
    }
    public function results()
    {
        return $this->hasMany(Result::class, 'exam_id', 'exam_id');
    }
  

}
