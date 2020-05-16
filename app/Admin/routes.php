<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->resource('edu-teachers', EduTeacherController::class);
    $router->resource('edu-students', EduStudentController::class);
    $router->get('edu-school/audit', 'EduAuditController@index');
    $router->post('edu-school/audit/{id}', 'EduAuditController@audit');
    $router->post('edu-student/chose/{id}', 'EduStudentController@chose');


});
