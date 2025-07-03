<?php

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Models\Setting;

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        $setting = cache()->remember("setting_$key", 3600, function () use ($key) {
            return Setting::where('key', $key)->value('value');
        });

        return $setting ?? $default;
    }
}
