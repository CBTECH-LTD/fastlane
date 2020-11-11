<?php

namespace CbtechLtd\Fastlane\Console\Commands;

use Carbon\Carbon;
use CbtechLtd\Fastlane\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Webmozart\Assert\Assert;

class GeneratePivotTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fastlane:entry-types:pivot {firstEntryType} {secondEntryType}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a migration to create a pivot table from the given Entry Types';

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
        $firstEntryTypeClass = $this->getEntryTypeClass('firstEntryType');
        $secondEntryTypeClass = $this->getEntryTypeClass('secondEntryType');

        Assert::implementsInterface(
            $firstEntryTypeClass,
            EntryType::class,
            'First argument must implement ' . EntryType::class
        );

        Assert::implementsInterface(
            $secondEntryTypeClass,
            EntryType::class,
            'First argument must implement ' . EntryType::class
        );

        $firstEntryTypeInstance = app()->make($firstEntryTypeClass);
        $secondEntryTypeInstance = app()->make($secondEntryTypeClass);

        if (is_null($firstEntryTypeInstance)) {
            throw new \Exception($firstEntryTypeClass . ' is not registered.');
        }

        if (is_null($secondEntryTypeInstance)) {
            throw new \Exception($secondEntryTypeClass . ' is not registered.');
        }

        $this->makeMigrationClass($firstEntryTypeInstance, $secondEntryTypeInstance);
    }

    protected function getEntryTypeClass(string $arg): string
    {
        $entryType = $this->argument($arg);

        $entryTypeDirectory = Str::endsWith($entryType, 'EntryType')
            ? Str::replaceLast('EntryType', '', $entryType)
            : $entryType;

        $entryTypeClass = Str::endsWith($entryType, 'EntryType')
            ? $entryType
            : "{$entryType}EntryType";

        if (class_exists($entryTypeFull = app()->getNamespace() . 'EntryTypes\\' . $entryTypeDirectory . '\\' . $entryTypeClass)) {
            return $entryTypeFull;
        }

        if (class_exists($entryType)) {
            return $entryType;
        }

        throw new \Exception('Entry Type ' . $entryType . ' does not exist.');
    }

    protected function makeMigrationClass(EntryType $firstEntryType, EntryType $secondEntryType): void
    {
        $names = [
            $firstEntryType->identifier(),
            $secondEntryType->identifier(),
        ];

        sort($names);

        $this->createClassFile($names, 'EntryPivotMigration');
    }

    protected function fileExists(string $file): bool
    {
        return File::exists($file);
    }

    /**
     * @param array  $names
     * @param string $stub
     * @throws \Exception
     */
    protected function createClassFile(array $names, string $stub): void
    {
        $className = 'Create' . Str::title($names[0]) . Str::title($names[1]) . 'PivotTable';

        $now = Carbon::now()->format('Y_m_d_Hmi');
        $filePath = database_path('migrations/' . $now . '_' . Str::snake($className) . '.php');

        if ($this->fileExists($filePath)) {
            throw new \Exception('File ' . $filePath . ' already exists');
        }

        $view = View::file(
            __DIR__ . '/../../../stubs/' . $stub . '.blade.php',
            [
                'class'          => $className,
                'table'          => Str::singular($names[0]) . '_' . Str::singular($names[1]),
                'table_one_name' => Str::plural($names[0]),
                'table_one_column' => Str::singular($names[0]) . '_id',
                'table_two_name' => Str::plural($names[1]),
                'table_two_column' => Str::singular($names[1]) . '_id',
            ]
        );

        $this->savePHPFile($filePath, $view);
    }

    protected function savePHPFile(string $filePath, string $content): void
    {
        File::put($filePath, '<?php' . PHP_EOL . PHP_EOL . $content);

        $this->info('File ' . $filePath . ' created.');
    }
}
