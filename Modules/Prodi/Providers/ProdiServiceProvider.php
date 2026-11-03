<?php

namespace Modules\Prodi\Providers;

use Illuminate\Support\ServiceProvider;

class ProdiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'prodi');
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }
}
