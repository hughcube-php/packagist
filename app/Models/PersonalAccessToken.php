<?php

namespace App\Models;

use App\Models\Base\PersonalAccessToken as BasePersonalAccessToken;

/**
 * @mixin BasePersonalAccessToken
 * @property  User $tokenable.
 */
class PersonalAccessToken extends \Laravel\Sanctum\PersonalAccessToken
{
    use AAATrait;

    protected $hidden = [
        'token'
    ];
}
