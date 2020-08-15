<?php

namespace CbtechLtd\Fastlane\Console\Commands;

use Carbon\Carbon;
use CbtechLtd\Fastlane\Support\Contracts\EntryType;
use CbtechLtd\Fastlane\Support\Contracts\SchemaField;
use CbtechLtd\Fastlane\Support\Schema\Fields\Contracts\Resolvable;
use CbtechLtd\Fastlane\Support\Schema\Fields\FieldPanel;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Webmozart\Assert\Assert;

class GenerateMigrationFromEntryTypeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fastlane:entry-types:migration {entryType}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a migration from the given Entry Type';

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
        $entryTypeClass = $this->getEntryTypeClass();

        Assert::implementsInterface(
            $entryTypeClass,
            EntryType::class,
            'First argument must implement ' . EntryType::class
        );

        $entryTypeInstance = app()->make($entryTypeClass);

        if (is_null($entryTypeInstance)) {
            throw new \Exception($entryTypeClass . ' is not registered.');
        }

        $this->makeMigrationClass($entryTypeInstance);
    }

    protected function getEntryTypeClass(): string
    {
        $entryType = $this->argument('entryType');

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

    protected function makeMigrationClass(EntryType $entryType): void
    {
        $className = 'Create' . $entryType->pluralName() . 'Table';
        $table = Str::plural(Str::snake($entryType->identifier(), '_'));

        $fields = Collection::make($entryType->fields())
            ->flatMap(function ($field) {
                if ($field instanceof FieldPanel) {
                    return $field->getFields();
                }

                return Arr::wrap($field);
            })
            ->filter(function (SchemaField $field) {
                return $field->getName() !== 'is_active';
            });

        $this->createClassFile($className, $table, get_class($entryType), $fields->all(), 'EntryMigration');
    }

    protected function fileExists(string $file): bool
    {
        return File::exists($file);
    }

    /**
     * @param string $className
     * @param string $table
     * @param string $entryTypeClass
     * @param array  $columns
     * @param string $stub
     * @throws \Exception
     */
    protected function createClassFile(string $className, string $table, string $entryTypeClass, array $columns, string $stub): void
    {
        $now = Carbon::now()->format('Y_m_d_Hmi');
        $filePath = database_path('migrations/' . $now . '_' . Str::snake($className) . '.php');

        if ($this->fileExists($filePath)) {
            throw new \Exception('File ' . $filePath . ' already exists');
        }

        $colDef = Collection::make($columns)->map(function (SchemaField $f) {
            $migration = $f->toMigration();

            return ! empty($migration)
                ? $migration
                : null;
        })->filter();

        $view = View::file(
            __DIR__ . '/../../../stubs/' . $stub . '.blade.php',
            [
                'class'          => $className,
                'table'          => $table,
                'entryTypeClass' => $entryTypeClass,
                'columns'        => $colDef,
            ]
        );

        $this->savePHPFile($filePath, $view);
    }

    protected function savePHPFile(string $filePath, string $content): void
    {
        File::put($filePath, '<?php' . PHP_EOL . PHP_EOL . $content);

        $this->comment('File ' . $filePath . ' created.');
    }
}
