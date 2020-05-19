<?php


namespace App\Models;


class LineAuthorize extends BaseModel
{
    protected $table = "edu_line_authorizes";

    public function scopeTeacher($query)
    {
        return $query->where('authorizes_type', 1);
    }

    public function scopeStudent($query)
    {
        return $query->where('authorizes_type', 1);
    }

    public function students()
    {
        return $this->belongsTo(EduStudent::class, 'edu_student_id', 'id');
    }

    public function teachers()
    {
        return $this->belongsTo(EduTeacher::class, 'edu_teacher_id', 'id');
    }
}