<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori_Silabus extends Model
{
    use HasFactory;
    protected $table = 'syllabus';
    protected $primaryKey = 'id_syllabus';
    // protected $fillable = [
    //     'id_kelas', 'judul', 'deskripsi'
    // ];
    protected $dateFormat = 'U';
}
