<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEduTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('edu_teachers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable()->comment('老师姓名');
            $table->string('avatar')->nullable()->comment('头像');
            $table->string('email')->unique()->index()->comment('登陆邮箱');
            $table->string('password', 60);
            $table->tinyInteger('verified')->default(0)->comment('是否验证邮箱0-未,1-通过');
            $table->rememberToken();
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
        Schema::dropIfExists('edu_teachers');
    }
}
