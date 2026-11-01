<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyllabusStudent extends Model
{
    use HasFactory;
    protected $table = 'syllabus_student';
    protected $primaryKey = 'id_syllabus_student';
    protected $fillable = [
        'id_class_student',
        'id_syllabus',
        'status_class',
        'created_at',
        'updated_at',
    ];
    protected $dateFormat = 'U';
}
