<?php

namespace Guardian360\Repository\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class SpecificationMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:specification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Guardian360 specification class.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Specification';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/specification.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Specifications';
    }
}
