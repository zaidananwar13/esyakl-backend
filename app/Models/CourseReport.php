<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseReport extends Model
{
    use HasFactory;

    protected $table = 'course_report';
    protected $primaryKey = 'id_course_report';
    protected $dateFormat = 'U';
}
