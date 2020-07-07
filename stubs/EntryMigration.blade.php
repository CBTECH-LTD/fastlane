use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class {{ $class }} extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('{{ $table }}', function (Blueprint $table) {
            // It creates:
            // - Incremental ID (big integer)
            // - hashid (string)
            // - date timestamps
            $table->cmsCommon();

            // It creates an is_active (boolean) column
            // to be used in companion of Activable trait.
            $table->activable();

            // Specific table columns...
            @isset($columns)

            collect(app()->make('{{ $entryTypeClass }}')->schema()->build()->getFields())
                ->each(function ($field) use ($table) {
                    if ($field->getName() !== 'is_active') {
                        $field->runOnMigration($table);
                    }
                });

            @endisset

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('{{ $table }}');
    }
}
