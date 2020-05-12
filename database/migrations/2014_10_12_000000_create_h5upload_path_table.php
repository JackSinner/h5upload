<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class  CreateH5uploadPathTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h5upload_path', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 200)->nullable(false)->default('')->comment('目录名称');
            $table->integer('pid')->comment('父id')->nullable(false)->default(0);
            $table->softDeletes();
            $table->timestamps();
            $table->index('pid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('h5upload_path');
    }
}
