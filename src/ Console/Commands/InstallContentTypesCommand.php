<?php

namespace CbtechLtd\Fastlane\Console\Commands;

use CbtechLtd\Fastlane\FastlaneFacade;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use Illuminate\Console\Command;

class InstallContentTypesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fastlane:content-types:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Content Types as configured in Fastlane config file';

    /**
     * Create a new command instance.
     *
     * @return void
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
        FastlaneFacade::entryTypes()->each(function (EntryType $contentType) {
            $this->comment('Installing ' . get_class($contentType));

            $contentType->install();
        });
    }
}
