<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectResult extends Model
{
    use HasFactory;
    protected $primaryKey = "id_project_result";
    protected $table = "project_result";
    protected $fillable = [
        'id_project_student', 'id_class_student', 'file', 'created_at', 'updated_at'
    ];
    protected $dateFormat = 'U';
}
