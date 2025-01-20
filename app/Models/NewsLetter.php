<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsLetter extends Model
{
    use HasFactory;

    
     protected $fillable = [
         'user_id', // 
         'name',
         'counts',
     ];
 
     protected $primaryKey = 'id';
 
     public $incrementing = true;
 
     protected $keyType = 'int';
}
