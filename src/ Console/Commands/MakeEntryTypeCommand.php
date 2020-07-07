<?php

namespace CbtechLtd\Fastlane\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class MakeEntryTypeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fastlane:entry-types:make {name} {--all} {--m|model} {--i|migration} {--p|policy} {--r|resource} {--s|schema}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a new Entry Type';

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
        $ns = app()->getNamespace() . 'EntryTypes\\' . $this->getNameArgument();

        // Generate the class structure.
        $entryDir = $this->makeFilePath('');

        if (! File::exists($entryDir)) {
            File::makeDirectory($entryDir);
        }

        $this->makeEntryTypeClass($ns);
        $this->makeModelClass($ns);
        $this->makePolicyClass($ns);
        $this->makeResourceClass($ns);
        $this->makeSchemaClass($ns);
        $this->makeMigrationClass($ns);
    }

    protected function getNameArgument(): string
    {
        return Str::studly(trim($this->argument('name')));
    }

    protected function makeEntryTypeClass(string $namespace): void
    {
        $className = $this->getNameArgument() . 'EntryType';

        $this->createClassFile($className, $namespace, 'EntryType');
    }

    protected function makeModelClass(string $namespace): void
    {
        if (! $this->option('model') && ! $this->option('all')) {
            return;
        }

        $className = $this->getNameArgument();
        $this->createClassFile($className, $namespace, 'EntryModel');
    }

    protected function makePolicyClass(string $namespace): void
    {
        if (! $this->option('policy') && ! $this->option('all')) {
            return;
        }

        $className = $this->getNameArgument() . 'Policy';
        $this->createClassFile($className, $namespace, 'EntryPolicy');
    }

    protected function makeSchemaClass(string $namespace): void
    {
        if (! $this->option('schema') && ! $this->option('all')) {
            return;
        }

        $className = $this->getNameArgument() . 'Schema';
        $this->createClassFile($className, $namespace, 'EntrySchema');
    }

    protected function makeResourceClass(string $namespace): void
    {
        if (! $this->option('resource') && ! $this->option('all')) {
            return;
        }

        $className = $this->getNameArgument() . 'Resource';
        $type = Str::snake($this->getNameArgument(), '-');

        $this->createClassFile($className, $namespace, 'EntryResource', [
            'type'      => $type,
            'routeName' => Str::plural($type),
        ]);
    }

    protected function makeMigrationClass(string $namespace): void
    {
        if (! $this->option('migration') && ! $this->option('all')) {
            return;
        }

        $className = 'Create' . Str::plural($this->getNameArgument()) . 'Table';
        $table = Str::plural(Str::snake($this->getNameArgument(), '_'));

        $now = Carbon::now()->format('Y_m_d_Hmi');
        $migrationPath = database_path('migrations/' . $now . '_' . Str::snake($className) . '.php');

        $this->createClassFile($className, $namespace, 'EntryMigration', [
            'table' => $table,
        ], $migrationPath);
    }

    protected function makeFilePath(string $relativeClass): string
    {
        return app_path('EntryTypes/' . $this->getNameArgument() . '/' . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass));
    }

    protected function fileExists(string $file): bool
    {
        return File::exists($file);
    }

    /**
     * @param string      $className
     * @param string      $namespace
     * @param string      $stub
     * @param array       $data
     * @param string|null $customPath
     * @throws \Exception
     */
    protected function createClassFile(string $className, string $namespace, string $stub, array $data = [], ?string $customPath = null): void
    {
        $filePath = $customPath ?? $this->makeFilePath($className . '.php');

        if ($this->fileExists($filePath)) {
            throw new \Exception('File ' . $filePath . ' already exists');
        }

        $view = View::file(
            __DIR__ . '/../../../stubs/' . $stub . '.blade.php',
            array_merge([
                'namespace' => $namespace,
                'class'     => $className,
            ], $data)
        );

        $this->savePHPFile($filePath, $view);
    }

    protected function savePHPFile(string $filePath, string $content): void
    {
        File::put($filePath, '<?php' . PHP_EOL . PHP_EOL . $content);

        $this->comment('File ' . $filePath . ' created.');
    }
}
