<?php

namespace jorenvanhocht\CreatePackages\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem as FileSystem;

class CreatePackageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:package
        {name : The name of the package}
        {vendorname? : Your vendor name (optional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new package structure';

    /**
     * @var string
     */
    protected $package;

    /**
     * @var string
     */
    protected $vendor;

    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Create a new command instance.
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     */
    public function __construct(Config $config, FileSystem $filesystem)
    {
        parent::__construct();

        $this->config = objectify($config->get('createpackages'));
        $this->filesystem = $filesystem;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->package = $this->argument('name');
        $this->vendor = $this->argument('vendorname');
        $this->createFolderStructure();

        // create empty service provider
        $this->createServiceProvider();
        // create empty facade
        // create empty package class

        // return psr4 autoloading line for in composer.json file
        // return service provider for in config/app.php
        // return facade for in config/app.php
    }

    private function createFolderStructure()
    {
        $baseFolder = $this->config->folder;
        $vendorname = $this->getVendorName();
        $basePath = "$baseFolder/$vendorname/$this->package";

        $this->filesystem->makeDirectory($basePath, 0755, true);
        $this->filesystem->makeDirectory("$basePath/src", 0755, true);
        $this->filesystem->makeDirectory("$basePath/tests", 0755, true);
        $this->filesystem->makeDirectory("$basePath/Providers", 0755, true);
    }

    /**
     * @return string
     */
    private function getVendorName()
    {
        if(! isset($this->vendor)) {
            return $this->config->vendorname;
        }

        return $this->vendor;
    }

    private function createServiceProvider()
    {
        $content = file_get_contents(__DIR__.'/../../templates/ServiceProvider.txt');
        $content = str_replace('{{vendorname}}', $this->getVendorName(), $content);
        $content = str_replace('{{packageName}}', ucfirst($this->package).'ServicePovider', $content);
        $file = $this->config->folder.'/'.$this->getVendorName().'/'.$this->package.'/Providers/'.ucfirst($this->package).'ServiceProvider.php';

        file_put_contents($file, $content);
    }
}
