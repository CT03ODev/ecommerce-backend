<?php

namespace App\Models\Traits;
use Illuminate\Support\Str;

trait Sluggable
{
    // Laravel' Eloquent model will boot a trait's method with the name of pattern boot[TraitName]
    public static function bootSluggable()
    {
        static::saving(function ($model) {
            if (!$model->slug) {
                $slug = Str::slug($model->name);
                if (self::where('slug', $slug)->exists()) {
                    $slug .= '-' . Str::random(4);
                }
                $model->slug = $slug;
            }
        });
    }
}