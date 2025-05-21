<?php

namespace App\Models;

use App\Models\Traits\ModelBlamer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasFactory, SoftDeletes, ModelBlamer;

    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'minimum_spend',
        'maximum_discount',
        'usage_limit',
        'usage_count',
        'start_date',
        'end_date',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'minimum_spend' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'usage_count' => 'integer',
        'usage_limit' => 'integer'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isValid()
    {
        $now = now();
        
        if (!$this->is_active) {
            return false;
        }

        if ($now->lt($this->start_date) || $now->gt($this->end_date)) {
            return false;
        }

        if ($this->usage_limit !== null && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($orderAmount)
    {
        if (!$this->isValid() || $orderAmount <= 0) {
            return 0;
        }

        if ($this->minimum_spend && $orderAmount < $this->minimum_spend) {
            return 0;
        }

        $discount = $this->discount_type === 'percentage'
            ? $orderAmount * ($this->discount_value / 100)
            : $this->discount_value;

        if ($this->maximum_discount) {
            $discount = min($discount, $this->maximum_discount);
        }

        return round($discount, 2);
    }
}