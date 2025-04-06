<?php

namespace App\Models;

use App\Models\Traits\ModelBlamer;
use App\Models\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    use Sluggable;
    use ModelBlamer;

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $appends = ['thumbnail_url', 'price'];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getThumbnailUrlAttribute()
    {
        $baseUrl = config('app.url');

        return $this->thumbnail 
            ? $baseUrl . '/storage/' . $this->thumbnail 
            : $baseUrl . '/images/default-thumbnail.jpg';
    }

    public function getPriceAttribute()
    {
        $firstVariant = $this->variants()->first();
        return $firstVariant ? $firstVariant->price : null;
    }
}
