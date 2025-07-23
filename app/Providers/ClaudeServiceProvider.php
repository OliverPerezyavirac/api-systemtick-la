<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ClaudeService;

class ClaudeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ClaudeService::class, function ($app) {
            return new ClaudeService();
        });
    }

    public function boot()
    {
        //
    }
} 