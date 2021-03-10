<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('core_files', function (Blueprint $table) {
            $table->engine = 'MyISAM';
            $table->id('file_id');
            $table->char('file_title', 250)->default('');
            $table->char('file_name', 150);
            $table->char('file_module', 50)->default('core')->index();
            $table->tinyInteger('file_user_id', false, true)->default(0)->index();
            $table->char('file_type', 50)->default('')->index();
            $table->char('file_folder', 200)->default('')->index();
            $table->string('file_sizes', 500);
            $table->integer('file_time', false, true)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('core_files');
    }
}
