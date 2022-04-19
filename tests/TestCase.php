<?php

namespace Xkairo\CacheRepositoryLaravel\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Xkairo\CacheRepositoryLaravel\CacheRepositoryServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            CacheRepositoryServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('paths.repositorie_interface_path', __DIR__ . '/Repositories');
        config()->set('paths.repositorie_eloquent_path', __DIR__ . '/Repositories/EloquentRepositories');
        config()->set('paths.repositorie_cache_path', __DIR__ . '/Cache');

        /*
        $migration = include __DIR__.'/../database/migrations/create_skeleton_table.php.stub';
        $migration->up();
        */
    }
}
