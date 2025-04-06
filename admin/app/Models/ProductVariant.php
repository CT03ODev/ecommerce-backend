<?php

namespace App\Models;

use App\Models\Traits\ModelBlamer;
use App\Models\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;
    use ModelBlamer;

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [
        'size',
        'color',
        'price',
        'weight',
        'stock_quantity',
        'product_id',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

}
