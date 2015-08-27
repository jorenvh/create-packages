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
        $this->createFacade();
        // create empty package class
        $this->createPackageClass();

        $this->info('Folder structure and classes sucesfully created');
        // return psr4 autoloading line for in composer.json file
        $this->info('Add this line to the PRS-4 autoloading section in your composer.json file');
        $this->info($this->getComposerJsonAutoloading());
        // return service provider for in config/app.php
        $this->info('');
        $this->info('Add this line to the providers array in config/app.php');
        $this->info($this->getServiceProvider());
        // return facade for in config/app.php
        $this->info('');
        $this->info('Add this line to the aliases array in config/app.php');
        $this->info($this->getFacade());
    }

    private function createFolderStructure()
    {
        $baseFolder = $this->config->folder;
        $vendorname = $this->getVendorName();
        $basePath = "$baseFolder/$vendorname/$this->package";

        $this->filesystem->makeDirectory($basePath, 0755, true);
        $this->filesystem->makeDirectory("$basePath/src", 0755, true);
        $this->filesystem->makeDirectory("$basePath/tests", 0755, true);
        $this->filesystem->makeDirectory("$basePath/src/Providers", 0755, true);
        $this->filesystem->makeDirectory("$basePath/src/Facades", 0755, true);
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
        $file = $this->config->folder.'/'.$this->getVendorName().'/'.$this->package.'/src/Providers/'.ucfirst($this->package).'ServiceProvider.php';

        file_put_contents($file, $content);
    }

    private function createFacade()
    {
        $content = file_get_contents(__DIR__.'/../../templates/Facade.txt');
        $content = str_replace('{{vendorname}}', $this->getVendorName(), $content);
        $content = str_replace('{{packageName}}', ucfirst($this->package).'Facade', $content);
        $content = str_replace('{{packageName2}}', strtolower($this->package), $content);
        $file = $this->config->folder.'/'.$this->getVendorName().'/'.$this->package.'/src/Facades/'.ucfirst($this->package).'Facade.php';

        file_put_contents($file, $content);
    }

    private function createPackageClass()
    {
        $content = file_get_contents(__DIR__.'/../../templates/PackageClass.txt');
        $content = str_replace('{{vendorname}}', $this->getVendorName(), $content);
        $content = str_replace('{{packageName}}', ucfirst($this->package), $content);
        $file = $this->config->folder.'/'.$this->getVendorName().'/'.$this->package.'/src/'.ucfirst($this->package).'.php';

        file_put_contents($file, $content);
    }

    private function getComposerJsonAutoloading()
    {
        return '"'.$this->getVendorName().'\\\\'.ucfirst($this->package).'": "'.$this->config->folder.'/'.$this->getVendorName().'/'.$this->package.'/src/"';
    }

    private function getServiceProvider()
    {
        return $this->getVendorName().'\\'.ucfirst($this->package).'\Providers\\'.ucfirst($this->package).'ServiceProvider::class,';
    }

    private function getFacade()
    {
        return $this->getVendorName().'\\'.ucfirst($this->package).'\Facades\\'.ucfirst($this->package).'Facade::class,';
    }
}
