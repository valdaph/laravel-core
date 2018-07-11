<?php

namespace Valda\Commands;

use Illuminate\Console\Command;
use Laravel\Scout\Searchable;
use ScoutElastic\Searchable as ElasticSearchable;

class ValdaScoutImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'valda-scout:import {--fresh : Recreate search indeces before importing.}
                {--create : Create search indices before importing.}  
                {--map : Update type mapping of indeces (For Elasticsearch only).}
                {--models= : The path to the models}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all models into the search index';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $models = $this->getSearchableModels();

        if ($this->option('create')) {
            $this->createIndices($models);
        } elseif ($this->option('fresh')) {
            $this->recreateIndices($models);
        }

        $this->importModels($models);
        $this->updateMapping($models);
    }

    /**
     * Create search indices.
     *
     * @param  array  $models
     * @return void
     */
    protected function createIndices($models)
    {
        foreach ($models as $model) {
            $baseModel = array_last(explode('\\', $model));
            $snakeModel = snake_case($baseModel);

            switch (config('scout.driver')) {
                case 'elastic':
                    $this->call('elastic:create-index', [
                        'index-configurator' => "App\IndexConfigurators\\{$baseModel}IndexConfigurator",
                    ]);
                    break;
            }
        }
    }

    /**
     * Get the class name of a given file.
     *
     * @param  string  $filePath
     * @return string
     */
    protected function getClassFromFile($filePath) {
        include_once $filePath;

        $classes = get_declared_classes();

        return end($classes);
    }

    /**
     * Get the path to the index configurator files.
     *
     * @return string
     */
    protected function getPathToIndexConfigurators()
    {
        return $this->hasArgument('index-configurators') && realpath($this->argument('index-configurators'))
            ? realpath($this->argument('index-configurators'))
            : base_path('app/IndexConfigurators');
    }

    /**
     * Get the path to the model files.
     *
     * @return string
     */
    protected function getPathToModels()
    {
        return $this->hasArgument('models') && realpath($this->argument('models'))
            ? realpath($this->argument('models'))
            : base_path('app/Models');
    }

    /**
     * Get all searchable model classes.
     *
     * @return array
     */
    protected function getSearchableModels()
    {
        $path = $this->getPathToModels();
        $files = scandir($path);

        return collect($files)
            ->map(function ($file) use ($path) {
                if ($file == '.' || $file == '..' || !preg_match('/\.php$/', $file)) {
                    return null;
                }

                $modelClass = $this->getClassFromFile("$path/$file");
                $modelTraits = class_uses($modelClass);

                if (in_array('ScoutElastic\Searchable', $modelTraits)
                    || in_array('Laravel\Scout\Searchable', $modelTraits)
                ) {
                    return $modelClass;
                }

                return null;
            })
            ->filter()
            ->values()
            ->toArray();
    }

    /**
     * Import models into the search index.
     *
     * @param  array  $models
     * @return void
     */
    protected function importModels($models)
    {
        config(['scout.queue' => false]);

        foreach ($models as $model) {
            switch (config('scout.driver')) {
                case 'elastic':
                    $this->call('scout:import', compact('model'));
                    break;
            }
        }
    }

    /**
     * Recreate search indices.
     *
     * @return void
     */
    protected function recreateIndices($models)
    {
        foreach ($models as $model) {
            $baseModel = array_last(explode('\\', $model));
            $snakeModel = snake_case($baseModel);

            switch (config('scout.driver')) {
                case 'elastic':
                    $this->call('elastic:drop-index', ['index-configurator' => "App\IndexConfigurators\\{$baseModel}IndexConfigurator"]);
                    $this->call('elastic:create-index', ['index-configurator' => "App\IndexConfigurators\\{$baseModel}IndexConfigurator"]);
                    break;
            }
        }
    }

    /**
     * Update type mapping of indeces.
     *
     * @return void
     */
    protected function updateMapping($models)
    {
        if (config('scout.driver') !== 'elastic') {
            return;
        }

        foreach ($models as $model) {
            $this->call('elastic:update-mapping', compact('model'));
        }
    }
}
