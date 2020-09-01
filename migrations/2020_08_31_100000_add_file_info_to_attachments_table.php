<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileInfoToAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fastlane_attachments', function (Blueprint $table) {
            $table->string('extension')->nullable();
            $table->string('size')->nullable();
            $table->string('mimetype')->nullable();
        });

        Schema::table('fastlane_draft_attachments', function (Blueprint $table) {
            $table->string('extension')->nullable();
            $table->string('size')->nullable();
            $table->string('mimetype')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fastlane_attachments', function (Blueprint $table) {
            $table->dropColumn('extension');
            $table->dropColumn('size');
            $table->dropColumn('mimetype');
        });

        Schema::table('fastlane_draft_attachments', function (Blueprint $table) {
            $table->dropColumn('extension');
            $table->dropColumn('size');
            $table->dropColumn('mimetype');
        });
    }
}
