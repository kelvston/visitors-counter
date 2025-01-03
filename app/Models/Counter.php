<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Counter extends Model
{
    use SoftDeletes;
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'counters';

    // Define which attributes are mass assignable
    protected $fillable = [
        'user_id', // Add the user_id field here
        'gender',
        'counts',
        // Add other fields as needed
    ];

    // Optionally, you can define the primary key if it's not 'id'
    protected $primaryKey = 'id';

    // Specify if the primary key is auto-incrementing
    public $incrementing = true;

    // Specify the type of the primary key
    protected $keyType = 'int';
}
