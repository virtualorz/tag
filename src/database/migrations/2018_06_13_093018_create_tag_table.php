<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->dateTime('created_at')->comment('建立資料時間');
            $table->dateTime('updated_at')->comment('最後編輯資料時間');
            $table->string('name', 12)->comment('標籤名稱');
            $table->tinyInteger('enable')->unsigned()->comment('0:停用 1:啟用');
            $table->dateTime('delete')->nullable()->comment('刪除時間');
            $table->integer('creat_admin_id')->unsigned()->nullable()->comment('建立資料管理員ID');
            $table->integer('update_admin_id')->unsigned()->nullable()->comment('最夠更新資料管理員ID');
        });
        Schema::create('tag_lang', function (Blueprint $table) {
            $table->bigInteger('tag_id')->unsigned()->comment('標籤ID');
            $table->string('lang',3)->comment('語言');
            $table->dateTime('created_at')->comment('建立資料時間');
            $table->dateTime('updated_at')->comment('最後編輯資料時間');
            $table->string('name', 12)->comment('標籤名稱');
            $table->integer('creat_admin_id')->unsigned()->nullable()->comment('建立資料管理員ID');
            $table->integer('update_admin_id')->unsigned()->nullable()->comment('最夠更新資料管理員ID');
            $table->primary(['tag_id', 'lang']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tag');
        Schema::dropIfExists('tag_lang');
    }
}
