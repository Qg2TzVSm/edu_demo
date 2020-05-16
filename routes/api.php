<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'prefix'        => '',
    'namespace'     => 'Api',
//    'middleware'    => '',
],function (){
    // 用户登陆
    Route::post('login','EduAuthController@login');
    // 老师注册
    Route::post('register','EduRegisterController@registerTeacher');

    Route::middleware(['auth:api','auth.custom'])->group(function (){
//        Route::post('test','TestController@test');
        // 绑定line账号
        Route::get('bind/prepare','EduAuthController@bindPrepare');
        /*********    老师部分START    **********/
        // 老师申请学校
        Route::post('school/register','EduRegisterController@registerSchool');
        // 老师获取自己创建成功的学校列表
        Route::get('q', 'EduInviteController@schools');
        // 老师邀请网站另外的老师成为指定学校的老师
        Route::post('invite', 'EduInviteController@invite');
        // 老师获取个人信息
        Route::get('teacher/profile', 'EduTeacherController@profile');
        // 老师获取指定学校学生列表
        Route::get('school/{school}/students', 'EduTeacherController@students');
        // 老师获取关注自己的学生列表
        Route::get('school/{school}/follows', 'EduTeacherController@follows');
        /*********     老师部分END      **********/
        // todo 增加中间件判断老师和学生

        /*********    学生部分START    **********/
        Route::group([
            "prefix" => "student",
        ], function (){
            // 学生获取个人信息
            Route::get('profile', 'EduStudentController@profile');
            // 学生获取学校老师列表
            Route::get('teachers', 'EduStudentController@teachers');
            // 学生获取关注列表
            Route::get('follows', 'EduStudentController@follows');
            // 学生关注老师
            Route::post('teacher/{teacher}/follow', 'EduStudentController@follow');
            // 学生取消关注老师
            Route::post('teacher/{teacher}/un-follow', 'EduStudentController@unFollow');
        });
        /*********     学生部分END      **********/

    });

});
