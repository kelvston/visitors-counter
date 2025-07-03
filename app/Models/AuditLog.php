<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id', 'changes', 'ip_address'
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getQuantityDetails()
    {
        if (!is_array($this->changes)) {
            return null;
        }

        if (isset($this->changes['quantity'])) {
            return [
                'before' => null,
                'after' => $this->changes['quantity'],
            ];
        }

        $beforeQty = $this->changes['before']['quantity'] ?? null;
        $afterQty = $this->changes['after']['quantity'] ?? null;

        if ($beforeQty !== null || $afterQty !== null) {
            return [
                'before' => $beforeQty,
                'after' => $afterQty,
            ];
        }

        return null;
    }

}
