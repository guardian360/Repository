<?php

namespace Guardian360\Repository\Providers;

use Illuminate\Support\ServiceProvider;
use Guardian360\Repository\Console\Commands\RepositoryMakeCommand;
use Guardian360\Repository\Console\Commands\SpecificationMakeCommand;

class RepositoryProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                RepositoryMakeCommand::class,
                SpecificationMakeCommand::class,
            ]);
        }
    }
}
