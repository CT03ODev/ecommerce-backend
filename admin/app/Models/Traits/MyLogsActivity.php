<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

trait MyLogsActivity
{
    // use LogsActivity;

    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //         ->logAll()
    //         ->logOnlyDirty();
    // }

    // public function logs(): HasMany {
    //     return $this->hasMany(Activity::class, 'subject_id')
    //         ->where(['subject_type' => self::class]);
    // }
}