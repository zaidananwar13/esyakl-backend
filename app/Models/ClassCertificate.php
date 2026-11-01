<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassCertificate extends Model
{
    use HasFactory;

    protected $table = 'class_certificate';
    protected $primaryKey = 'id_class_certificate';
    protected $fillable = [
        'id_certificate', 'id_class_student', 'file', 'token', 'created_at', 'updated_at'
    ];
    protected $dateFormat = 'U';
    public $incrementing = true;
}
