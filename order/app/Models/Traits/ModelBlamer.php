<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

trait ModelBlamer
{
    // Laravel' Eloquent model will boot a trait's method with the name of pattern boot[TraitName]
    public static function bootModelBlamer()
    {
        // updating created_by and updated_by when model is created
        static::creating(function ($model) {
            // if (!$model->isDirty('created_by') && auth()->user()) {
            //     $model->created_by = auth()->user()->id;
            // }
            // if (!$model->isDirty('updated_by') && auth()->user()) {
            //     $model->updated_by = auth()->user()->id;
            // }
            if (Schema::hasColumn($model->getTable(), 'uuid')) {
                $model->uuid = Str::random(16);
            }
        });

        // updating updated_by when model is updated
        // static::updating(function ($model) {
        //     if (!$model->isDirty('updated_by') && auth()->user()) {
        //         $model->updated_by = auth()->user()->id;
        //     }
        // });

        // deleting updated_by when model is updated
        // static::deleting(function ($model) {
        //     if (
        //         in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive(static::class), true)
        //         && !$model->isDirty('deleted_by') && auth()->user()
        //     ) {
        //         $model->deleted_by = auth()->user()->id;
        //         $model->save(); // Try this to force the update regardless of timing
        //     }
        // });
    }

    // public function author(): BelongsTo {
    //     return $this->belongsTo(User::class, 'created_by');
    // }

    // public function creator(): BelongsTo {
    //     return $this->belongsTo(User::class, 'created_by');
    // }

    // public function remover(): BelongsTo {
    //     return $this->belongsTo(User::class, 'deleted_by');
    // }

    // public function editor(): BelongsTo {
    //     return $this->belongsTo(User::class, 'updated_by');
    // }
}