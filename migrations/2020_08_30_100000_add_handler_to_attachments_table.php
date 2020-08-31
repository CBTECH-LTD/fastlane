<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHandlerToAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fastlane_attachments', function (Blueprint $table) {
            $table->string('handler')->nullable();
        });

        Schema::table('fastlane_draft_attachments', function (Blueprint $table) {
            $table->string('handler')->nullable();
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
            $table->dropColumn('handler');
        });

        Schema::table('fastlane_draft_attachments', function (Blueprint $table) {
            $table->dropColumn('handler');
        });
    }
}
