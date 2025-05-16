<?php

namespace Sdk\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class UnitMake extends GeneratorCommand
{
    protected $name = 'make:unit';

    protected $description = 'Create a new form unit class';

    protected $type = 'Unit';

    protected function getStub()
    {
        if ($this->option('modelSave')) {
            return $this->resolveStubPath('/stubs/unit.model.save.stub');
        }

        return $this->resolveStubPath('/stubs/unit.stub');
    }

    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . $stub;
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Cores\Unit';
    }

    protected function getOptions()
    {
        return [
            ['modelSave', 'modelSave', InputOption::VALUE_NONE, 'Create a new model save unit'],
        ];
    }

    public function replaceClass($stub, $name)
    {
        if ($this->option('modelSave')) {
            $name = $this->argument('name');
            $modelName = Str::before(Str::afterLast($name, '\\'), 'SaveUnit');
            $modelCamelName = Str::camel($modelName);
            $stub = str_replace(['{{ modelName }}'], $modelName, $stub);
            $stub = str_replace(['{{ modelCamelName }}'], $modelCamelName, $stub);
            // request 和 response
            $fields = app('App\Models\\' . $modelName)::init()->getFillable();
            $fieldRequest = '';
            $fieldResponse = '';
            foreach ($fields as $field) {
                $camelField = Str::camel($field);
                $studlyField = Str::studly($field);
                $fieldRequest .= str_pad('', 4) . 'public function setRequest' . $studlyField . '($' . $camelField . ')' . PHP_EOL;
                $fieldRequest .= str_pad('', 4) . '{' . PHP_EOL;
                $fieldRequest .= str_pad('', 8) . '$this->data->setItem(\'' . $modelCamelName . '.' . $camelField . '\', $' . $camelField . ');' . PHP_EOL;
                $fieldRequest .= str_pad('', 8) . 'return $this;' . PHP_EOL;
                $fieldRequest .= str_pad('', 4) . '}' . PHP_EOL;
                $fieldRequest .= PHP_EOL;

                $fieldResponse .= PHP_EOL;
                $fieldResponse .= str_pad('', 4) . 'public function getResponse' . $studlyField . '()' . PHP_EOL;
                $fieldResponse .= str_pad('', 4) . '{' . PHP_EOL;
                $fieldResponse .= str_pad('', 8) . 'return $this->data->getItem(\'' . $modelCamelName . '.' . $camelField . '\');' . PHP_EOL;
                $fieldResponse .= str_pad('', 4) . '}' . PHP_EOL;
            }
            $stub = str_replace(['{{ fieldRequest }}'], $fieldRequest, $stub);
            $stub = str_replace(['{{ fieldResponse }}'], $fieldResponse, $stub);
        }
        return parent::replaceClass($stub, $name);
    }
}

