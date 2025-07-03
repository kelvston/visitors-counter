<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_item_id',
        'receipt_number',
        'receipt_data',
        'total_amount',
        'user_id',
        'printed_at',
    ];

    protected $casts = [
        'printed_at' => 'datetime',
        'receipt_data' => 'array', // if you're storing JSON
    ];

    // Relationships

    public function saleItem()
    {
        return $this->belongsTo(Sale::class, 'sale_item_id'); // If you meant SaleItem model, change this to SaleItem::class
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
