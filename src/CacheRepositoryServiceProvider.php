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
}
