<?php

namespace App\Models;

use App\Models\Traits\ModelBlamer;
use App\Models\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    use Sluggable;
    use ModelBlamer;

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    protected $fillable = [
        'name',
        'slug',
        'description',
        'thumbnail',
        'content',
        'is_published',
    ];
    protected $appends = ['thumbnail_url'];
    protected $attributes = [
        'is_published' => true,
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function getThumbnailUrlAttribute()
    {
        $baseUrl = config('app.url');

        return $this->thumbnail 
            ? $baseUrl . '/storage/' . $this->thumbnail 
            : $baseUrl . '/images/default-thumbnail.jpg';
    }
}
