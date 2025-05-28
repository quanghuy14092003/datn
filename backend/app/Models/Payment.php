<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'transaction_id',
        'payment_method',
        'amount',
        'status',
        'response_code',
        'secure_hash',
    ];
    // Model Payment
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
