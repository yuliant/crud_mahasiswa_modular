<?php

namespace Modules\Mahasiswa\Providers;

use Illuminate\Support\ServiceProvider;

class MahasiswaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'mahasiswa');
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }
}
