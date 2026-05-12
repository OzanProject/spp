<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['invoice_id', 'amount', 'method', 'bank_name', 'sender_name', 'paid_at', 'proof', 'status', 'note', 'reject_reason'];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
