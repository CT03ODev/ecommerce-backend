<?php

namespace App\Models;

use App\Models\Traits\ModelBlamer;
use App\Models\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;
    use Sluggable;
    use ModelBlamer;

    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'is_published'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
