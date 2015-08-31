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

        $this->createServiceProvider();
        $this->createFacade();
        $this->createPackageClass();

       $this->returnOutput();
    }

    /**
     * Create the package directories
     *
     */
    private function createFolderStructure()
    {
        $baseFolder = $this->config->folder;
        $vendorname = $this->getVendorName();
        $basePath = "$baseFolder/$vendorname/$this->package";

        $this->filesystem
            ->makeDirectory($basePath, 0755, true);
        $this->filesystem
            ->makeDirectory("$basePath/src", 0755, true);
        $this->filesystem
            ->makeDirectory("$basePath/tests", 0755, true);
        $this->filesystem
            ->makeDirectory("$basePath/src/Providers", 0755, true);
        $this->filesystem
            ->makeDirectory("$basePath/src/Facades", 0755, true);
    }

    /**
     * Get the given vendor name
     *
     * @return string
     */
    private function getVendorName()
    {
        if(! isset($this->vendor)) {
            return $this->config->vendorname;
        }

        return $this->vendor;
    }

    /**
     * Create the service provider class
     *
     */
    private function createServiceProvider()
    {
        $content = file_get_contents(__DIR__.'/../../templates/ServiceProvider.txt');
        $content = $this->replaceNamespace($content, 'ServiceProvider');
        $file = $this->getFilePath('Providers', 'ServiceProvider');
        file_put_contents($file, $content);
    }

    /**
     * Create the facade class
     *
     */
    private function createFacade()
    {
        $content = file_get_contents(__DIR__.'/../../templates/Facade.txt');
        $content = $this->replaceNamespace($content, 'Facade');
        $content = str_replace('{{packageName2}}', strtolower($this->package), $content);
        $file = $this->getFilePath('Facades', 'Facade');
        file_put_contents($file, $content);
    }

    /**
     * Create the base package class
     *
     */
    private function createPackageClass()
    {
        $content = file_get_contents(__DIR__.'/../../templates/PackageClass.txt');
        $content = $this->replaceNamespace($content, '');
        $file = $this->getFilePath('', '');
        file_put_contents($file, $content);
    }

    /**
     * Generate the psr4 autoloading line that needs to be added
     * to the composer.json file of the project
     *
     * @return string
     */
    private function getComposerJsonAutoloading()
    {
        return '"'.$this->getVendorName().'\\\\'.ucfirst($this->package).
            '": "'.$this->config->folder.'/'.$this->getVendorName().'/'.
            $this->package.'/src/"';
    }

    /**
     * Get the service provider that needs to be added to
     * the config/app.php file
     *
     * @return string
     */
    private function getServiceProvider()
    {
        return $this->getVendorName().'\\'.ucfirst($this->package).
            '\Providers\\'.ucfirst($this->package).
            'ServiceProvider::class,';
    }

    /**
     * Get the alias that needs to be added to
     * the config/app.php file
     *
     * @return string
     */
    private function getFacade()
    {
        return $this->getVendorName().'\\'.ucfirst($this->package).'\Facades\\'.
            ucfirst($this->package).'Facade::class,';
    }

    /**
     * Fill in the correct namespace in a template file
     *
     * @param $content
     * @param $folder
     * @return mixed
     */
    private function replaceNamespace($content, $folder)
    {
        $content = str_replace('{{vendorname}}', $this->getVendorName(), $content);
        $content = str_replace('{{packageName}}', ucfirst($this->package). $folder , $content);

        return $content;
    }

    /**
     * Get the full path where the generated file needs to be placed
     *
     * @param $folder
     * @param $suffix
     * @return string
     */
    private function getFilePath($folder, $suffix)
    {
        return $this->config->folder.'/'.$this->getVendorName().'/'.
            $this->package."/src/$folder/".ucfirst($this->package).
            "$suffix.php";
    }

    /**
     * Output info to the user
     *
     */
    private function returnOutput()
    {
        $this->comment('Folder structure and classes sucesfully created');

        $this->comment('Add this line to the PRS-4 autoloading section in your composer.json file');
        $this->info($this->getComposerJsonAutoloading());

        $this->info('');
        $this->comment('Add this line to the providers array in config/app.php');
        $this->info($this->getServiceProvider());

        $this->info('');
        $this->comment('Add this line to the aliases array in config/app.php');
        $this->info($this->getFacade());
    }
}
