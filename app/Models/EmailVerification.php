<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    use HasFactory;
    protected $table = 'email_verification';
    protected $primaryKey = 'id_email_verification';
    protected $fillable = ['id_user', 'email', 'token', 'expired_at'];
    protected $dateFormat = 'U';
}
