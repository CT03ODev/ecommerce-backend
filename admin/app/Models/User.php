<?php

namespace App\Models;

use App\Models\Traits\ModelBlamer;
use App\Models\Traits\MyLogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use SoftDeletes;
    use MyLogsActivity;
    use ModelBlamer;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function getProfilePhotoUrlAttribute() {
        if ($this->profile_photo_path) {
            return asset('storage/'.$this->profile_photo_path);
        }

        return 'https://ui-avatars.com/api/?background=27272a&color=fff&name=' . urlencode($this->name);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(Role::ROLE_SUPER_ADMIN);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(Role::ROLE_ADMIN);
    }

    public function isContentCreator(): bool
    {
        return $this->hasRole(Role::ROLE_CONTENT_CREATOR);
    }

    public function isUser(): bool
    {
        return $this->hasRole(Role::ROLE_USER);
    }
}
