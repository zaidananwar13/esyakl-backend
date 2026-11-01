<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    use HasFactory;
    protected $table = 'user_log';
    protected $primaryKey = 'id_user_log';
    protected $fillable = [
        'id_user', 'status', 'time'
    ];
    public $timestamps = false;
}
