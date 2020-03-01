<?php

namespace App\Supports;

use Illuminate\Support\Facades\Auth;

class PermissionSupport
{
    public static function can($permission, $user = null)
    {
        if (Auth::guest() && $user==null) {
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
        return false;
    }
}
