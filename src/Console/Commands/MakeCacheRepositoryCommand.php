<?php

namespace Xkairo\CacheRepositoryLaravel\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Xkairo\CacheRepositoryLaravel\FileManager;

class MakeCacheRepositoryCommand extends Command
{

    protected $repositorieInterfacePath;
    protected $repositorieEloquentPath;
    protected $repositorieCachePath;
    protected $templatePath;

    public function __construct()
    {
        $this->repositorieInterfacePath = Config::get('paths.repositorie_interface_path');
        $this->repositorieEloquentPath = Config::get('paths.repositorie_eloquent_path');
        $this->repositorieCachePath = Config::get('paths.repositorie_cache_path');
        $this->templatePath = __DIR__ . '/../../Templates';

        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Repository';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->createPaths();

        $name = $this->argument('model');

        //Create Interface Repository
        $this->createFile($name, $this->templatePath . '/ModelRepositoryInterface.stub', $this->repositorieInterfacePath . "/$name" . "RepositoryInterface.php", 'Repository Interface');
        //Create Eloquent Repository
        $this->createFile($name, $this->templatePath . '/EloquentModelRepository.stub', $this->repositorieEloquentPath . "/$name" . "Repository.php", 'Eloquent Repository');
        //Create Cache Repository
        $this->createFile($name, $this->templatePath . '/ModelCacheRepository.stub', $this->repositorieCachePath . "/$name" . "CacheRepository.php", 'Cache Repository');
        //Changing bindings in App Service Provider
        $this->chargeBindInAppProvider($name);
        //Comment in console the code for the Controller
        $this->getCodeForController($name, $this->templatePath . '/ControllerModelRepository.stub');
    }

    public function chargeBindInAppProvider($name)
    {
        $appProviderPath = app_path('/Providers/AppServiceProvider.php');

        $appServiceFile = file_get_contents($appProviderPath);

        $newFileContent = "";

        if ($appServiceFile) {
            $regExpNamespace = '/(namespace.*?;)/';
            $regExpBind = '/(\$bindings.*?=.*?\[)/';
            $regExpBeginningClassAppServiceProvider = "/(class.*?AppServiceProvider.*?\n{)/";

            if (preg_match($regExpNamespace, $appServiceFile)) {
                $importInterfaceCode = "use App\Repositories\\" . "$name" . "RepositoryInterface;";
                $importEloquentRepositoryCode = "use App\Repositories\EloquentRepositories\\" . $name . "Repository;";

                $importBindings = "\n" . $importEloquentRepositoryCode . "\n" . $importInterfaceCode;
                if (strpos($appServiceFile, $importBindings) === false) $newFileContent = preg_replace($regExpNamespace, '${1}' . "\n" . $importBindings, $appServiceFile);
            } else {
                $this->error('App Service Provider has a invalid format');
            }

            if (preg_match($regExpBind, $appServiceFile)) {
                $codeBinding = '${1}' . "\n\t\t" . $name . "RepositoryInterface::class => " . $name . "Repository::class,";
                if (strpos($appServiceFile, $codeBinding) === false) $newFileContent = preg_replace($regExpBind, $codeBinding, $newFileContent ?? $appServiceFile);
            } else if (preg_match($regExpBeginningClassAppServiceProvider, $appServiceFile)) {
                $codeBinding = '${1}' . "\n\n\tpublic \$bindings = [\n\t\t" . $name . "RepositoryInterface::class => " . $name . "Repository::class,\n\t];\n";
                $newFileContent = preg_replace($regExpBeginningClassAppServiceProvider, $codeBinding, $newFileContent ?? $appServiceFile);
            } else {
                $this->error('App Service Provider has a invalid format');
            }

            file_put_contents($appProviderPath, $newFileContent);

            $this->info("AppServiceProvider changed succesfully");
        } else {
            $this->error('Error opening App Service Provider');
        }
    }

    public function createPaths()
    {
        if (!is_dir($this->repositorieEloquentPath)) {
            mkdir($this->repositorieEloquentPath, 0777, true);
        }

        if (!is_dir($this->repositorieCachePath)) {
            mkdir($this->repositorieCachePath, 0777, true);
        }

        if (!is_dir($this->repositorieInterfacePath)) {
            mkdir($this->repositorieInterfacePath, 0777, true);
        }
    }

    public function createFile($name, $model_path, $objetive_path, $file_type)
    {
        //Open model File
        $gestorModel = fopen($model_path, 'r');
        if ($gestorModel) {
            if (!file_exists($objetive_path)) {
                //Open Objetive Repository
                $gestorNewRepository = fopen($objetive_path, 'w');

                $this->replaceNameModelForFile($gestorModel, $name, function ($line_replaced) use ($gestorNewRepository) {
                    fwrite($gestorNewRepository, $line_replaced);
                });

                fclose($gestorNewRepository);

                $this->info("$file_type created succesfully");
            } else {
                $this->comment("$file_type Already Exists");
            }
            fclose($gestorModel);
        } else {
            $this->error('Error opening Model Repository');
        }
    }

    public function getCodeForController($name, $model_path)
    {
        $gestorModel = fopen($model_path, 'r');
        if ($gestorModel) {
            $code = "";
            $this->replaceNameModelForFile($gestorModel, $name, function ($line_replaced) use (&$code) {
                $code .= $line_replaced;
            });
            fclose($gestorModel);
            $this->comment('Copy this code in your controller');
            $this->comment($code);
        } else {
            $this->error('Error opening Model Repository');
        }
    }

    public function replaceNameModelForFile($gestorModel, $name, $closure)
    {
        //Write Objetive Repository with Model replacing NameModel
        while ($line = fgets($gestorModel)) {
            $line_replaced = str_replace("NameModel", ucfirst($name), $line);
            $line_replaced = str_replace("name_model", lcfirst($name), $line_replaced);
            $closure($line_replaced);
        }
    }
}
