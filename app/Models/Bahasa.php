<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bahasa extends Model
{
    use HasFactory;

    protected $table = 'bahasa';
    protected $primaryKey = 'id_bahasa';
    protected $fillable = [
        'bahasa', 'created_at', 'updated_at'
    ];
    protected $dateFormat = 'U';
}
