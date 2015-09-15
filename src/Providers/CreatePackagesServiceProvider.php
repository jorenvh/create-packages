<?php

namespace jorenvanhocht\CreatePackages\Providers;

use Illuminate\Support\ServiceProvider;

class CreatePackagesServiceProvider extends ServiceProvider {

    /**
     * Register the service provider
     *
     */
    public function register()
    {
        //
    }

    /**
     * Load the resources
     *
     */
    public function boot()
    {
        $this->commands([
            'jorenvanhocht\CreatePackages\Commands\CreatePackageCommand',
        ]);

        $this->publishes([
            __DIR__.'/../../config' => config_path(),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/../../config/createpackages.php', 'createpackages');
    }

}