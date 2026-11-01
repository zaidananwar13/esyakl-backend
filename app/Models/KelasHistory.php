<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelasHistory extends Model
{
    use HasFactory;
    
    protected $table = 'kelas_history';
    protected $primaryKey = 'id_history';
    protected $fillable = [
        'id_user', 'id_kelas', 'id_kategori_slilabus',  'id_sub_kategori_silabus', 'created_at', 'updated_at'
    ];
    // protected $dateFormat = 'U';
}
