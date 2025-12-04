<?php
// app/Providers/RouteServiceProvider.php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckUserRole;

class RouteServiceProvider extends ServiceProvider
{
        protected $middlewareAliases = [
        // 'auth' => \App\Http\Middleware\Authenticate::class, // Already defined by Laravel 
        'role' => CheckUserRole::class, 
    ];
    
    // ... rest of the class methods ...
}
