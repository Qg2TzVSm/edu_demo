<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEduLineUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('edu_line_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('openid')->index()->comment('line openid');
            $table->string('email')->nullable()->comment('line 用户email');
            $table->string('line_user_id')->nullable()->comment('line user id');
            $table->string('avatar')->nullable()->comment('line avatar');
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
        Schema::dropIfExists('edu_line_users');
    }
}
