<?php

namespace App\Models;

use App\Models\Traits\ModelBlamer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;
    use ModelBlamer;

    protected $fillable = [
        'image',
        'sort_order',
        'product_variant_id',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $appends = ['image_url'];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getImageUrlAttribute()
    {
        return $this->image 
            ? asset('storage/' . $this->image) 
            : asset('images/default-product-image.jpg');
    }
}
