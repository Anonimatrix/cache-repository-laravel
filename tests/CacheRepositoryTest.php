<?php

namespace Xkairo\CacheRepositoryLaravel\Tests;

use Illuminate\Support\Facades\Config;
use Xkairo\CacheRepositoryLaravel\Console\Commands\MakeCacheRepositoryCommand;
use Xkairo\CacheRepositoryLaravel\Tests\TestCase;

class CacheRepositoryTest extends TestCase
{
    public function test_creation_files()
    {
        $model = "Subcategorie";

        $this->deleteRepositorieFiles($model);
        $this->deletePaths();

        $this->artisan('make:repository', ["model" => $model])
            ->assertSuccessful()
            ->expectsOutput("Repository Interface created succesfully")
            ->expectsOutput('Eloquent Repository created succesfully')
            ->expectsOutput('Cache Repository created succesfully');

        $this->deleteRepositorieFiles($model);
        $this->deletePaths();
    }

    //TODO test_app_service_provider

    public function test_not_create_if_exists()
    {
        $model = "Subcategorie";

        $this->deleteRepositorieFiles($model);
        $this->deletePaths();

        $this->artisan('make:repository', ["model" => $model])
            ->assertSuccessful();

        $this->artisan('make:repository', ["model" => $model])
            ->assertSuccessful()
            ->expectsOutput("Repository Interface Already Exists");

        $this->deleteRepositorieFiles($model);
        $this->deletePaths();
    }

    public function deletePaths()
    {
        if (is_dir(Config::get('paths.repositorie_eloquent_path'))) {
            rmdir(Config::get('paths.repositorie_eloquent_path'));
        }

        if (is_dir(Config::get('paths.repositorie_interface_path'))) {
            rmdir(Config::get('paths.repositorie_interface_path'));
        }

        if (is_dir(Config::get('paths.repositorie_cache_path'))) {
            rmdir(Config::get('paths.repositorie_cache_path'));
        }
    }

    public function deleteRepositorieFiles($model)
    {
        $interfaceFileName = Config::get('paths.repositorie_interface_path') . "/$model" . "RepositoryInterface.php";
        if (file_exists($interfaceFileName)) {
            unlink($interfaceFileName);
        }

        $eloquentFileName = Config::get('paths.repositorie_eloquent_path') . "/$model" . "Repository.php";
        if (file_exists($eloquentFileName)) {
            unlink($eloquentFileName);
        }

        $cacheFileName = Config::get('paths.repositorie_cache_path') . "/$model" . "CacheRepository.php";
        if (file_exists($cacheFileName)) {
            unlink($cacheFileName);
        }
    }
}
