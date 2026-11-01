<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectUser extends Model
{
    use HasFactory;
    protected $primaryKey = "id_project_student";
    protected $table = "project_student";
    protected $fillable = [
        'id_class_student', 'id_project', 'id_user', 'expired', 'note', 'poin', 'created_at', 'updated_at'
    ];
    protected $dateFormat = 'U';
}
