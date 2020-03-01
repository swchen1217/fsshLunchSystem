<?php

namespace App\Supports;

use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;

class PermissionSupport
{
    public static function check($permission, $user = null, $throw = false)
    {
        if (Auth::guest() && $user == null) {
            if ($throw)
                throw UnauthorizedException::notLoggedIn();
            return false;
        }
        if ($user == null)
            $user = Auth::user();
        $parts = explode('.', $permission);
        $ability = '';
        foreach ($parts as $part) {
            $ability .= $ability ? '.' . $part : $part;
            if ($user->can($ability)) {
                return true;
            }
        }
        if ($throw)
            throw UnauthorizedException::forPermissions($permission);
        return false;
    }
}
