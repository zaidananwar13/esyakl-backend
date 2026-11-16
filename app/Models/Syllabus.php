<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Syllabus extends Model
{
    use HasFactory;
    protected $table = 'syllabus';
    protected $primaryKey = 'id_syllabus';
    protected $dateFormat = 'U';

    /**
     * Scope query to exclude empty/draft syllabi with null or blank titles.
     */
    public function scopeValid($query)
    {
        return $query->whereNotNull('title')->where('title', '!=', '');
    }
}
