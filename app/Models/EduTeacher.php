<?php


namespace App\Models;

use Carbon\Carbon;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Role;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class EduTeacher extends Authenticatable
{
    use HasApiTokens, Notifiable;
    protected $table = 'edu_teachers';
    protected $guarded = [];

    /**
     * 创建老师
     * @param $teacher_name
     * @param $email
     * @param $password
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public static function createTeacher($teacher_name, $email, $password)
    {
        return self::query()->create([
            'name' => $teacher_name,
            'password' => bcrypt($password),
            'email' => $email,
        ]);
    }

    public static function attachSchool(EduTeacher $teacher, $school_id, $is_creator)
    {
        $teacher->schools()->attach($school_id,
            [
                'is_creator' => $is_creator,
                'created_at' => new Carbon(),
                'updated_at' => new Carbon(),
            ]
        );
    }

    /**
     * 判断老师是否有这个学生
     * @param EduTeacher $teacher
     * @param EduStudent $student
     * @return bool
     */
    public static function ifHasThisStudent(EduTeacher $teacher, EduStudent $student)
    {
        return self::belongToSchool($teacher, $student->school->id);
    }

    /**
     * 老师是否属于指定学校
     * @param EduTeacher $teacher
     * @param $school_id
     * @return bool
     */
    public static function belongToSchool(EduTeacher $teacher, $school_id)
    {
        return !empty($teacher->schools()->where('school_id', $school_id)->first());
    }

    /**
     * 老师是否是学校的管理员
     * @param EduTeacher $teacher
     * @param $school_id
     * @return bool
     */
    public static function isMasterOfSchool(EduTeacher $teacher, $school_id)
    {
        return !empty($teacher->schools()->where('is_creator', 1)->where('school_id', $school_id)->first());
    }

    /**
     * 为老师创建后台管理账号
     * @param EduTeacher $teacher
     * @param int $role_type
     */
    public static function createBackendAccountForTeacher(EduTeacher $teacher, $role_type=1)
    {
        try{
//            $pass = str_random(6);
            // 一个老师可能同时是一个学校的管理员又是另外一个学校的管理员或普通老师 但邮箱地址唯一
            $pass = 'secret';
            $user = Administrator::updateOrCreate([
                'username' => $teacher->email,
            ],[
                'password' => bcrypt($pass),
                'name'     => $teacher->name,
            ]);
            $role = Role::query()
                ->where('name', $role_type===1 ? 'SchoolAdministrator' : 'SchoolTeacher')->first();
            $roles_before = $user->roles()->pluck('id')->toArray();
            $user->roles()->sync(array_unique(array_merge($roles_before, array($role->id))));
            // todo 将创建好的老师登陆信息发送给用户，邮件或站内信，为了方便直接设置为secret
        }catch (\Exception $e){

        }
    }

    public function schools()
    {
        return $this->belongsToMany('App\Models\EduSchool', 'edu_school_teachers', 'teacher_id', 'school_id');
    }

    public function followed()
    {
        return $this->belongsToMany('App\Models\EduStudent', 'edu_follows', 'teacher_id', 'student_id');
    }
}