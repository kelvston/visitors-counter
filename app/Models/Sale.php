<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $table = 'sale_items';
    protected $fillable = [
        'product_id',
        'quantity',
        'unit_price', // Add this
        'subtotal',   // Add this
        'unit_type',
        'user_id',// Add this if you follow the recommendation above
    ];

    // Define relationship with Product model
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
