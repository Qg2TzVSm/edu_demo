<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\EduSchool;
use App\Models\EduTeacher;

class EduRegisterController extends Controller
{
    public function registerTeacher()
    {
        try{
            $teacher = EduTeacher::createTeacher(request('name'), request('email'), request('pass'));
            return $this->ok();
        }catch (\Exception $e){
            return $this->failed("注册失败！");
        }
    }

    public function registerSchool()
    {
        try{
            $user = request()->user();
            if (!($user instanceof EduTeacher)){
                return $this->failed('非法请求！');
            }
            $school = EduSchool::createSchool(request('schoolName'));
            EduTeacher::attachSchool($user, $school->id, 1);
            return $this->ok();
        }catch (\Exception $exception){
            return $this->failed("注册失败！");
        }
    }
}