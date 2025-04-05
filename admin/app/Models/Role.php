<?php

namespace App\Models;

use App\Models\Traits\MyLogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;
    use MyLogsActivity;

    const ROLE_SUPER_ADMIN = 'Super Admin';
    const ROLE_ADMIN = 'Admin';
    const ROLE_CONTENT_CREATOR = 'Content Creator';
    const ROLE_USER = 'User';

    public static $ROLES = [
        'SUPER_ADMIN' => self::ROLE_SUPER_ADMIN, 
        'ADMIN' => self::ROLE_ADMIN,
        'CONTENT_CREATOR' => self::ROLE_CONTENT_CREATOR,
        'USER' => self::ROLE_USER,
    ];
}
