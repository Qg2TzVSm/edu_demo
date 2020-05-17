<?php

use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        $teacher_admin = Role::query()->create([
            'name' => 'SchoolAdministrator',
            'slug' => 'school_administrator',
        ]);
        $normal_teacher = Role::query()->create([
            'name' => 'SchoolTeacher',
            'slug' => 'school_teacher',
        ]);
        $permission_1 = Permission::query()->create([
            "name" => "学校审核",
            "slug" => "school-audit",
            "http_method" => "GET,POST",
            "http_path" => "/edu-school/audit\r\n/edu-school/audit/*",
        ]);
        $permission_2 = Permission::query()->create([
            "name" => "学生查看",
            "slug" => "student-manage",
            "http_method" => "GET",
            "http_path" => "/edu-students",
        ]);
        $permission_3 = Permission::query()->create([
            "name" => "老师查看",
            "slug" => "teacher-manage",
            "http_method" => "GET",
            "http_path" => "/edu-teachers",
        ]);
        $permission_4 = Permission::query()->create([
            "name" => "管理员老师的权限",
            "slug" => "switch-school",
            "http_method" => "",
            "http_path" => "/edu-student/chose/*\r\n/edu-students\r\n/edu-students/*",
        ]);
        $teacher_admin->permissions()->saveMany([$permission_2, $permission_3, $permission_4]);
        $normal_teacher->permissions()->save($permission_2);

    }
}
