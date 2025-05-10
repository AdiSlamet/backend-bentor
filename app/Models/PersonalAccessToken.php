<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    /**
     * Set connection based on tokenable type
     */
    public function getConnectionName()
    {
        return match($this->tokenable_type) {
            'App\Models\Admin\Admin' => 'mysql_admin',
            'App\Models\Driver\Driver' => 'mysql_driver',
            'App\Models\User\Penumpang' => 'mysql_user',
            default => config('database.default'),
        };
    }
}