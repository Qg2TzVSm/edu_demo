<?php

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
//        DB::table('edu_roles')->truncate();
//        DB::table('edu_roles')->insert(
//            [
//                ['id'=>1, 'name' => '学校管理员'],
//                ['id'=>2, 'name' => '老师'],
//                ['id'=>3, 'name' => '学生'],
//            ]
//        );
//        \App\Models\EduTeacher::create([
//            'email' => 'test@test.com',
//            'password' => bcrypt('secret')
//        ]);
//        \App\Models\EduStudent::create([
//            'email' => 'test1@test.com',
//            'password' => bcrypt('secret')
//        ]);
        //Artisan::call('passport:install');
        \Encore\Admin\Auth\Database\Role::query()->insert([
            [
                'name' => 'SchoolAdministrator',
                'slug' => 'school_administrator',
            ],
            [
                'name' => 'SchoolTeacher',
                'slug' => 'school_teacher',
            ]
        ]);
    }
}
