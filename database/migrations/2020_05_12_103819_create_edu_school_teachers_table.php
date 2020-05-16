<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEduSchoolTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('edu_school_teachers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('school_id')->index()->comment('学校id');
            $table->unsignedInteger('teacher_id')->index()->comment('老师id');
            $table->tinyInteger('is_creator')->default(0)->comment('是否管理员');
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
        Schema::dropIfExists('edu_school_teachers');
    }
}
