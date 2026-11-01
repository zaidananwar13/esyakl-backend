<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sub_Kategori_Silabus extends Model
{
    use HasFactory;
    protected $table = 'sub_kategori_silabus';
    protected $primaryKey = 'id_sub_kategori_silabus';
    protected $fillable = [
        'id_kategori_silabus', 'id_bahasa', 'judul', 'deskripsi', 'konten'
    ];
    // protected $dateFormat = 'U';
}
