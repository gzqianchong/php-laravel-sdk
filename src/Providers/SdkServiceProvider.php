<?php

namespace Sdk\Providers;

use App\Console\Commands\ModuleMake;
use Illuminate\Support\ServiceProvider;
use Sdk\Console\Commands\ControllerMake;
use Sdk\Console\Commands\FeatureMake;
use Sdk\Console\Commands\UnitMake;

class SdkServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                UnitMake::class,
                FeatureMake::class,
                ControllerMake::class,
                ModuleMake::class,
            ]);
        }
    }
}
