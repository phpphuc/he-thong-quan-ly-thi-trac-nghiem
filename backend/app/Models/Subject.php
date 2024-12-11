<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $table = 'subjects'; 
    protected $primaryKey = 'subject_id'; 

    protected $fillable = [
        'subject_id',
        'name',
    ];
   

    public function questions()
    {
        return $this->hasMany(Question::class, 'subject_id', 'subject_id');
    }
    public function classes()
    {
        return $this->hasMany(Classroom::class, 'subject_id', 'subject_id');
    }

}
