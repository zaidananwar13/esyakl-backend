<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogLike extends Model
{
    use HasFactory;

    protected $table = 'blog_like';
    protected $primaryKey = 'id_blog_like';
    protected $dateFormat = 'U';
}
