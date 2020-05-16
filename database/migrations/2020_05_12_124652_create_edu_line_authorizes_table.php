<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEduLineAuthorizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('edu_line_authorizes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('line_user_id')->index()->comment('line user 表主键id');
            $table->tinyInteger('authorizes_type')->default(0)->comment('授权类型，0学生1老师');
            $table->unsignedInteger('edu_teacher_id')->default(0)->index()->comment('老师主键id');
            $table->unsignedInteger('edu_student_id')->default(0)->index()->comment('学生主键id');
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
        Schema::dropIfExists('edu_line_authorizes');
    }
}
