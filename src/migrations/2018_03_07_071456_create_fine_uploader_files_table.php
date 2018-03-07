<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFineUploaderFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fine_uploader_files', function (Blueprint $table) {
            $table->uuid('id');
            $table->integer('user_id')->unsigned()->comment('用户id');
            $table->string('name')->default('')->comment('文件名');
            $table->string('path')->default('')->comment('路径');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fine_uploader_files');
    }
}
