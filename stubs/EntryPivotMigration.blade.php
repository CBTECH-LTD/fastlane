use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class {{ $class }} extends Migration
{
    /**
     * Run the migrations.
     * @return void
    */
    public function up()
    {
        Schema::create('{{ $table }}', function (Blueprint $table) {
            $table->unsignedBigInteger('{{ $table_one_column }}');
            $table->unsignedBigInteger('{{ $table_two_column }}');
            $table->timestamps();

            $table->unique(['{{ $table_one_column }}', '{{ $table_two_column }}']);
            $table->foreign('{{ $table_one_column }}')->references('id')->on('{{ $table_one_name }}')->cascadeOnDelete();
            $table->foreign('{{ $table_two_column }}')->references('id')->on('{{ $table_two_name }}')->cascadeOnDelete();
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
