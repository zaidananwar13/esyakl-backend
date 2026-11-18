<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated Legacy orphan model. Replaced by App\Models\ClassStudent (table: class_student).
 */
class Kelas_User extends Model
{
    use HasFactory;
    protected $table = 'kelas_user';
    protected $primaryKey = 'id_kelas_user';
    protected $fillable = [
        'id_kelas', 'id_user', 'point_review', 'komentar_review'
    ];
    protected $dateFormat = 'U';
    public $timestamps = false;
}
