<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateH5uploadResourceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h5upload_resources', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key', 300)->comment('第三方存储名称')->default('')->nullable(false);
            $table->integer('path_id')->comment('目录id')->default(0)->nullable(false);
            $table->integer('size')->comment('文件大小字节')->default(0)->nullable(true);
            $table->softDeletes();
            $table->timestamps();
            $table->index('path_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('h5upload_resources');
    }
}
