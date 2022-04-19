<?php

namespace Xkairo\CacheRepositoryLaravel;

use Illuminate\Support\ServiceProvider;
use Xkairo\CacheRepositoryLaravel\Console\Commands\MakeCacheRepositoryCommand;

class CacheRepositoryServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadConfig();
        $this->loadCommands();
    }

    private function loadCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeCacheRepositoryCommand::class,
            ]);
        }
    }

    private function loadConfig(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/paths.php',
            'paths'
        );

        $this->publishes(
            [
                __DIR__ . '/../config/paths.php' => config_path('paths.php'),
            ],
            'cache-repositorie-config'
        );
    }
}
