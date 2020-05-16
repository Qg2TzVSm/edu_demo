<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEduSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('edu_schools', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('学校名称');
            $table->tinyInteger('verified')->default(0)->comment('是否审核0-初始,1-通过，2-不通过');
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
        Schema::dropIfExists('edu_schools');
    }
}
