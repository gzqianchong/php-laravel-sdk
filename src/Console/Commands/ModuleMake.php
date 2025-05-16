<?php

namespace Sdk\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleMake extends Command
{
    protected $signature = 'make:module {name?} {{--api}}';

    protected $description = 'Command description';

    public function handle()
    {
        if (empty($this->argument('name'))) {
            $directory = base_path('/app/Models');
            $files = File::files($directory);
            foreach ($files as $file) {
                $name = Str::beforeLast($file->getFilename(), '.');
                if (in_array($name, ['Model', 'PersonalAccessToken'])) {
                    continue;
                }
                $this->call('make:module', [
                    'name' => $name,
                ]);
            }
            return;
        }
        $name = Str::studly($this->argument('name'));
        $this->call('make:unit', [
            'name' => $name . '\\' . $name . 'SaveUnit',
            '--modelSave' => true,
        ]);
        $module = 'Api';
        if ($this->option('api')) {
            $module = 'Api';
        }
        if (!empty($module)) {
            $this->call('make:feature', [
                'name' => $module . '\\' . $name . '\\' . $name . 'GetFeature',
                '--httpGet' => true,
            ]);
            $this->call('make:feature', [
                'name' => $module . '\\' . $name . '\\' . $name . 'CreateFeature',
                '--httpCreate' => true,
            ]);
            $this->call('make:feature', [
                'name' => $module . '\\' . $name . '\\' . $name . 'UpdateFeature',
                '--httpUpdate' => true,
            ]);
            $this->call('make:feature', [
                'name' => $module . '\\' . $name . '\\' . $name . 'DetailFeature',
                '--httpDetail' => true,
            ]);
            $this->call('make:feature', [
                'name' => $module . '\\' . $name . '\\' . $name . 'AllFeature',
                '--httpAll' => true,
            ]);

            $this->call('make:controller', [
                'name' => $module . '\\' . $name . 'Controller',
            ]);
        }
    }
}
