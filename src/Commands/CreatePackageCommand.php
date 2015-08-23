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
    }

    private function createFolderStructure()
    {
        $baseFolder = $this->config->folder;
        $vendorname = $this->getVendorName();
        $fullPath = "$baseFolder/$vendorname/$this->package";

        $this->filesystem->makeDirectory($fullPath, 0755, true);
    }

    private function getVendorName()
    {
        if(! isset($this->vendor)) {
            return $this->config->vendorname;
        }

        return $this->vendor;
    }
}
