<?php


namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class EduStudent extends Authenticatable
{
    use HasApiTokens, Notifiable;
    protected $guarded = [];

    public function school()
    {
        return $this->belongsTo(EduSchool::class, 'school_id', 'id');
    }

    public function teachers(EduStudent $student)
    {
        return optional($student->school())->teachers();
    }

    public function following()
    {
        return $this->belongsToMany('App\Models\EduTeacher', 'edu_follows', 'student_id', 'teacher_id');
    }

    public static function ifHasFollowTheTeacher(EduStudent $student, $teacher_id)
    {
        return !empty($student->following()->where('teacher_id', $teacher_id)->first());
    }
}