<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizProgress extends Model
{
    use HasFactory;
    
    protected $table = 'quiz_student';
    protected $primaryKey = 'id_quiz_student';
    // protected $fillable = [
    //     'id_kategori_silabus', 'id_user', 'created_at', 'updated_at'
    // ];
    protected $dateFormat = 'U';
}
