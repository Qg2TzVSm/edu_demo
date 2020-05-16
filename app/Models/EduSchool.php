<?php


namespace App\Models;


class EduSchool extends BaseModel
{
    protected $table = 'edu_schools';

    public static function createSchool($name)
    {
        return self::query()->create(['name' => $name]);
    }

    public function teachers()
    {
        return $this->belongsToMany('App\Models\EduTeacher', 'edu_school_teachers', 'school_id', 'teacher_id');
    }

    public function students()
    {
        return $this->hasMany(EduStudent::class, 'school_id', 'id');
    }
}