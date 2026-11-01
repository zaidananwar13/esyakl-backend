<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForgotPassword extends Model
{
    use HasFactory;

    protected $table = 'forgot_password';
    protected $primaryKey = 'id_forgot_password';
    protected $dateFormat = 'U';

    protected $fillable = [
        'id_user',
        'token',
        'expired_at',
    ];

    protected $hidden = [
        'token',
    ];
}
