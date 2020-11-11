<?php

namespace CbtechLtd\Fastlane\Console\Commands;

use CbtechLtd\Fastlane\Contracts\EntryType;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CacheEntryTypeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fastlane:entry-types:cache {type?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache items of one or all entry types';

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
        $entryType = $this->getEntryType();

        if (is_null($entryType)) {
            throw new \Exception('Entry Type is not registered.');
        }

        $entryType->query()
            ->disableCache()
            ->getBuilder()
            ->cursor()
            ->each(function (Model $model) {
                //
            });
    }

    protected function getEntryType(): EntryType
    {
        $entryType = $this->argument('entryType');

        if (! $entryType) {
            return app('fastlane')->entryTypes();
        }

        $entryTypeDirectory = Str::endsWith($entryType, 'EntryType')
            ? Str::replaceLast('EntryType', '', $entryType)
            : $entryType;

        $entryTypeClass = Str::endsWith($entryType, 'EntryType')
            ? $entryType
            : "{$entryType}EntryType";

        if (class_exists($entryTypeFull = app()->getNamespace() . 'EntryTypes\\' . $entryTypeDirectory . '\\' . $entryTypeClass)) {
            return app('fastlane')->getEntryTypeByClass($entryTypeFull);
        }

        if (class_exists($entryType)) {
            return app('fastlane')->getEntryTypeByClass($entryType);
        }

        throw new \Exception('Entry Type ' . $entryType . ' does not exist.');
    }
}
