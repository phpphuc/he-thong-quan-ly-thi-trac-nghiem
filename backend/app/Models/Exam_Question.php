<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam_Question extends Model
{
    use HasFactory;
    protected $table = 'exam_question';
    protected $fillable = [
        'exam_id',
        'question_id',
    ];
     
    public function exam() {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function question() {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
