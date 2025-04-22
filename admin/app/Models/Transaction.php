<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'payment_id',
        'transaction_type',
        'gateway_transaction_id',
        'amount',
        'currency',
        'status',
        'gateway_response',
        'gateway_error',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'gateway_error' => 'array',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}