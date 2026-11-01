<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizStudent extends Model
{
    use HasFactory;
    protected $primaryKey = "id_quiz_student";
    protected $table = "quiz_student";
    protected $fillable = [
        'id_quiz', 'id_class_student', 'grade', 'created_at', 'updated_at'
    ];
    protected $dateFormat = 'U';
}
