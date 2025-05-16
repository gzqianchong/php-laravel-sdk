<?php

namespace Sdk\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class FeatureMake extends GeneratorCommand
{
    protected $name = 'make:feature';

    protected $description = 'Create a new form feature class';

    protected $type = 'Feature';

    protected function getStub()
    {
        if ($this->option('httpGet')) {
            return $this->resolveStubPath('/stubs/feature.get.stub');
        }

        if ($this->option('httpCreate')) {
            return $this->resolveStubPath('/stubs/feature.create.stub');
        }

        if ($this->option('httpUpdate')) {
            return $this->resolveStubPath('/stubs/feature.update.stub');
        }

        if ($this->option('httpDetail')) {
            return $this->resolveStubPath('/stubs/feature.detail.stub');
        }

        if ($this->option('httpAll')) {
            return $this->resolveStubPath('/stubs/feature.all.stub');
        }

        return $this->resolveStubPath('/stubs/feature.stub');
    }

    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Cores\Feature';
    }

    protected function getOptions()
    {
        return [
            ['httpGet', 'httpGet', InputOption::VALUE_NONE, 'Create a new get feature'],
            ['httpCreate', 'httpCreate', InputOption::VALUE_NONE, 'Create a new create feature'],
            ['httpUpdate', 'httpUpdate', InputOption::VALUE_NONE, 'Create a new update feature'],
            ['httpDetail', 'httpDetail', InputOption::VALUE_NONE, 'Create a new detail feature'],
            ['httpAll', 'httpAll', InputOption::VALUE_NONE, 'Create a new all feature'],
        ];
    }

    public function replaceClass($stub, $name)
    {
        if ($this->option('httpGet')) {
            $name = $this->argument('name');
            $modelName = Str::before(Str::afterLast($name, '\\'), 'GetFeature');
            $stub = str_replace(['{{ modelName }}'], $modelName, $stub);
        }
        if ($this->option('httpCreate')) {
            $name = $this->argument('name');
            $modelName = Str::before(Str::afterLast($name, '\\'), 'CreateFeature');
            $stub = str_replace(['{{ modelName }}'], $modelName, $stub);
        }
        if ($this->option('httpUpdate')) {
            $name = $this->argument('name');
            $modelName = Str::before(Str::afterLast($name, '\\'), 'UpdateFeature');
            $modelCamelName = Str::camel($modelName);
            $stub = str_replace(['{{ modelName }}'], $modelName, $stub);
            $stub = str_replace(['{{ modelCamelName }}'], $modelCamelName, $stub);
        }
        if ($this->option('httpDetail')) {
            $name = $this->argument('name');
            $modelName = Str::before(Str::afterLast($name, '\\'), 'DetailFeature');
            $modelCamelName = Str::camel($modelName);
            $stub = str_replace(['{{ modelName }}'], $modelName, $stub);
            $stub = str_replace(['{{ modelCamelName }}'], $modelCamelName, $stub);
        }
        if ($this->option('httpAll')) {
            $name = $this->argument('name');
            $modelName = Str::before(Str::afterLast($name, '\\'), 'AllFeature');
            $modelCamelName = Str::camel($modelName);
            $stub = str_replace(['{{ modelName }}'], $modelName, $stub);
            $stub = str_replace(['{{ modelCamelName }}'], $modelCamelName, $stub);
        }
        return parent::replaceClass($stub, $name);
    }
}
