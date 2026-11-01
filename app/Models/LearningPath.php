<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningPath extends Model
{
    use HasFactory;
    
    protected $table = 'learning_path';
    protected $primaryKey = 'id_learning_path';
    protected $dateFormat = 'U';
    
    public function newQuery($excludeDeleted = true)
    {
        if(isset($_GET['lang'])) {
            if(($_GET['lang']) == 'English') {
                return parent::newQuery($excludeDeleted)
                    ->where('id_bahasa', '2');
                    // ->where('status', '!=', 'draft'); 
            }else {
                return parent::newQuery($excludeDeleted)
                    ->where('id_bahasa', '1');
                    // ->where('status', '!=', 'draft'); 
            }
        }
        return parent::newQuery($excludeDeleted);
    }
}
