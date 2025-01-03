<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolBoard extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
     public function exams()
    {
        return $this->hasMany(Exam::class,'school_board_id', 'id');  
    }
    public function results()
    {
        return $this->hasManyThrough(Result::class, Exam::class, 'school_board_id', 'exam_id');
    }
}
