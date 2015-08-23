<?php

namespace jorenvanhocht\CreatePackages\Commands;

use Illuminate\Console\Command;

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
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
