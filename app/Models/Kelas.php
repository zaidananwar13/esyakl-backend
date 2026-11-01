<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;
    protected $table = 'class';
    protected $primaryKey = 'id_class';
    protected $dateFormat = 'U';

    public function newQuery($excludeDeleted = true)
    {
        if(isset($_GET['lang'])) {
            if(($_GET['lang']) == 'English') {
                return parent::newQuery($excludeDeleted)
                    ->where('id_bahasa', '2')
                    ->where('status', '!=', 'draft'); 
            }else {
                return parent::newQuery($excludeDeleted)
                    ->where('id_bahasa', '1')
                    ->where('status', '!=', 'draft'); 
            }
        }
        return parent::newQuery($excludeDeleted);
    }
}
