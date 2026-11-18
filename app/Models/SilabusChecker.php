<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated Legacy model. Access tracking handled by App\Models\SyllabusStudent (table: syllabus_student).
 */
class SilabusChecker extends Model
{
    use HasFactory;
    
    protected $table = 'silabus_checker';
    protected $primaryKey = 'id_silabus_checker';
    protected $fillable = [
        'id_user', 'id_kategori_silabus', 'id_sub_kategori_silabus', 'created_at', 'updated_at'
    ];
    // protected $dateFormat = 'U';
}
