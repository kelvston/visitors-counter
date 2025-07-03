<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    protected $fillable = ['category', 'amount', 'description', 'date'];

    public const CATEGORIES = [
        'electricity',
        'food',
        'transport',
        'cleanliness',
        'tra',
        'rent',
        'others',
    ];
}
