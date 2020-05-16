<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\EduStudent;
use App\Models\EduTeacher;

class EduStudentController extends Controller
{
    /**
     * 获取学生的用户资料
     * @return mixed
     */
    public function profile()
    {
        $student = request()->user();
        $profile = [
            'id' => $student->id,
            'avatar' => $student->avatar,
            'name' => $student->name,
            'email' => $student->email,
            'school' => [
                'id' => $student->school->id,
                'name' => $student->school->name,
            ],
        ];
        return $this->message($profile);
    }

    /**
     * 学生关注老师
     * @param EduTeacher $teacher
     * @return mixed
     */
    public function follow(EduTeacher $teacher)
    {
        $student = request()->user();
        if (!EduTeacher::ifHasThisStudent($teacher, $student)){
            return $this->failed('非法请求！');
        }
        $check = EduStudent::ifHasFollowTheTeacher($student, $teacher->id);
        if ($check){
            return $this->failed('已关注该老师！');
        }
        $student->following()->attach($teacher->id);
        return $this->ok();
    }

    /**
     * 取消关注老师
     * @param EduTeacher $teacher
     * @return mixed
     */
    public function unFollow(EduTeacher $teacher)
    {
        $student = request()->user();
        $student->following()->detach($teacher->id);
        return $this->ok();
    }

    /**
     * 关注列表
     * @return mixed
     */
    public function follows()
    {
        $student = request()->user();
        $following = $student->following()
            ->select(['edu_teachers.id', 'name', 'email', 'avatar'])
            ->get()->makeHidden('pivot')->toArray();
        return $this->message($following);
    }

    /**
     * 本学校所有老师
     * @return mixed
     */
    public function teachers()
    {
        $student = request()->user();
        return $this->message(
            $student->school->teachers()
                ->select(['edu_teachers.id', 'name', 'email', 'avatar'])
                ->withCount(['followed'=>function($q)use($student){
                    return $q->where('student_id', $student->id);
                }])->get()->makeHidden('pivot')->toArray()
        );
    }

}