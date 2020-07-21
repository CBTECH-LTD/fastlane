<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachmentsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fastlane_attachments', function (Blueprint $table) {
            $table->id();
            $table->morphs('attachable');
            $table->string('file');
            $table->string('url')->index();
            $table->timestamps();
        });

        Schema::create('fastlane_draft_attachments', function (Blueprint $table) {
            $table->id();
            $table->string('draft_id')->index();
            $table->string('file');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fastlane_attachments');
        Schema::dropIfExists('fastlane_draft_attachments');
    }
}