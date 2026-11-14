<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'title',
        'timeline',
        'timeline_from',
        'timeline_to',
        'destination',
        'details',
        'attachment',
    ];

    protected function casts(): array
    {
        return [
            'timeline_from' => 'date:Y-m-d',
            'timeline_to' => 'date:Y-m-d',
        ];
    }

}
