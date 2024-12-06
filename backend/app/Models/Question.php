<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'examid',
        'examname',
        'subjectid',
        'teacherid',
        'time',
        'examtype',
        'Qtype1',
        'Qtype2',
        'Qtype3',
        'Qnumber',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class, 'examid');
    }
}
