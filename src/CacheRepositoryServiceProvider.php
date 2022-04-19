<?php

namespace Xkairo\CacheRepositoryLaravel;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Xkairo\CacheRepositoryLaravel\Console\Commands\MakeCacheRepositoryCommand;

class CacheRepositoryServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('cache-repository-laravel')
            ->hasCommand(MakeCacheRepositoryCommand::class);
    }

    public function boot(): void
    {
        $this->loadConfig();
    }

    private function loadConfig(): void
    {
        $this->publishes(
            [
                __DIR__ . '/../config/paths.php' => config_path('paths.php'),
            ],
            'cache-repositorie-config'
        );
    }
}
