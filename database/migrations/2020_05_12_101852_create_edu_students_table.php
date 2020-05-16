<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEduStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('edu_students', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable()->comment('学生姓名');
            $table->string('avatar')->nullable()->comment('头像');
            $table->string('email')->unique()->index()->comment('学生登陆邮箱');
            $table->string('password', 60);
            $table->unsignedInteger('school_id')->default(0)->index()->comment('所属学校');
            $table->unsignedInteger('role_id')->default(0)->comment('学生角色');
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
        Schema::dropIfExists('edu_students');
    }
}
