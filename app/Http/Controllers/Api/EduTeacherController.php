<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\EduSchool;
use App\Models\EduTeacher;

class EduTeacherController extends Controller
{
    /**
     * 获取用户资料
     * @return mixed
     */
    public function profile()
    {
        $teacher = request()->user();
        $profile = [
            'id' => $teacher->id,
            'avatar' => $teacher->avatar,
            'name' => $teacher->name,
            'email' => $teacher->email,
            // 获取该老师所属的所有学校
            'schools' => array_values($teacher->schools()->get()
                ->where('verified', 1)->pluck('name','id')
                ->map(function ($k,$v){
                    return array('id'=>$v, 'name'=>$k);
                })->toArray()),
        ];
        return $this->message($profile);
    }

    /**
     * 获取指定学校下的学生列表
     * @param EduSchool $school
     * @return mixed
     */
    public function students(EduSchool $school)
    {
        // 校验老师是否属于该学校
        if (!EduTeacher::belongToSchool(request()->user(), $school->id)){
            return $this->failed('请求非法');
        }
        // 为了简单 直接取所有学生
        return $this->message($school->students()->select(['id', 'name', 'email', 'avatar'])->get()->toArray());
    }

    /**
     * 获取指定学校下关注自己的学生列表
     * @param EduSchool $school
     * @return mixed
     */
    public function follows(EduSchool $school)
    {
        $teacher = request()->user();
        // 简单无分页
        $followed = $teacher->followed()
            ->where('edu_students.school_id', $school->id)
            ->select(['edu_students.id', 'name', 'email', 'avatar'])
            ->get()->makeHidden('pivot')->toArray();
        return $this->message($followed);
    }
}