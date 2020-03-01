<?php

namespace App\Supports;

use Illuminate\Support\Facades\Auth;

class PermissionSupport
{
    public static function can($permission)
    {
        //TODO $user=Auth::user()
        if (Auth::guest()) {
            return false;
        }
        $parts = explode('.', $permission);
        $ability = '';
        foreach ($parts as $part) {
            $ability .= $ability ? '.' . $part : $part;
            if (Auth::user()->can($ability)) {
                return true;
            }
        }
        return false;
    }
}
