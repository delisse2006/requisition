<?php

namespace App\Services;

class BackgroundService
{
    public static function getLoginBackground()
    {
        return asset('images/backgrounds/login.jpg');
    }
    
    public static function getDashboardBackground()
    {
        return asset('images/backgrounds/dashboard.jpg');
    }
}