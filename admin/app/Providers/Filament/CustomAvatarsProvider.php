<?php

namespace App\Providers\Filament;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class CustomAvatarsProvider implements AvatarProvider
{
    public function get(Model | Authenticatable $record): string
    {
        if (!empty($record->profile_photo_path)) {
            return $record->profile_photo_url;
        }

        $name = str(Filament::getNameForDefaultAvatar($record))
            ->trim()
            ->explode(' ')
            ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
            ->join(' ');
 
        return 'https://ui-avatars.com/api/?background=27272a&color=fff&name=' . urlencode($name);
    }
}