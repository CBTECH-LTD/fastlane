<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFastlaneFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fastlane_files', function (Blueprint $table) {
            $table->cmsCommon();
            $table->string('name');
            $table->string('file')->index();
            $table->string('extension', 10)->nullable();
            $table->string('size', 20)->nullable();
            $table->string('mimetype', 50)->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();

            $table->foreign('parent_id')
                ->references('id')
                ->on('fastlane_files')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fastlane_files');
    }
}
