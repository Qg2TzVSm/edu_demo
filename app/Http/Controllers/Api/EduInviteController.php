<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\EduSchool;
use App\Models\EduTeacher;

class EduInviteController extends Controller
{
    /**
     * 老师邀请另外的老师成为自己学校的老师
     * @return mixed
     */
    public function invite()
    {
        // 为了简单，跳过搜索流程、直接用传上来的老师email进行操作，并跳过审核
        $teacher = $this->searchTeacher();
        if ($teacher->id == request()->user()->id){
            return $this->failed('不能邀请自己');
        }
        $schools = optional(request()->user()->schools())->where('is_creator', 1)->pluck('edu_schools.id')->toArray();
        $school_id = intval(request('school_id'));
        if (!in_array($school_id, $schools)){
            return $this->failed('操作非法！');
        }
        $school = EduSchool::query()->find($school_id);
        if ($school->verified !== 1){
            return $this->failed('该学校未审核通过！');
        }

        $check = EduTeacher::belongToSchool($teacher, $school_id);
        if ($check){
            return $this->failed('该用户已是此学校老师！');
        }
        EduTeacher::attachSchool($teacher, $school_id, 0);
        EduTeacher::createBackendAccountForTeacher($teacher, 0);
        return $this->ok();
    }

    /**
     * 老师创建的学校列表
     * @return mixed
     */
    public function schools()
    {
        $schools = optional(request()->user()->schools())
            ->where('is_creator', 1)
            ->get()
            ->where('verified', 1)
            ->pluck('name','id')->map(function ($k,$v){
                return array('id'=>$v, 'name'=>$k);
            })->toArray();
        return $this->message(array_values($schools));
    }

    protected function searchTeacher()
    {
        return EduTeacher::query()->where('email', request('email'))->firstOrFail();
    }
}