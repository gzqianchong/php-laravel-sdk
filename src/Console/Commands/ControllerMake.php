<?php

namespace Sdk\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class ControllerMake extends GeneratorCommand
{
    protected $name = 'make:controller';

    protected $description = 'Create a new form controller class';

    protected $type = 'Controller';

    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/controller.stub');
    }

    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\\Controllers';
    }

    public function replaceClass($stub, $name)
    {
        $name = $this->argument('name');
        $modelName = Str::before(Str::afterLast($name, '\\'), 'Controller');
        $modelCamelName = Str::camel($modelName);
        $stub = str_replace(['{{ modelName }}'], $modelName, $stub);
        $stub = str_replace(['{{ modelCamelName }}'], $modelCamelName, $stub);
        return parent::replaceClass($stub, $name);
    }
}
