<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEduFollowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('edu_follows', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('student_id')->index()->comment('学生id');
            $table->unsignedInteger('teacher_id')->index()->comment('老师id');
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
        Schema::dropIfExists('edu_follows');
    }
}
