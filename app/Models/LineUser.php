<?php


namespace App\Models;


class LineUser extends BaseModel
{
    protected $table = "edu_line_users";

    public function teacherAuthorizes()
    {
        return $this->hasMany(LineAuthorize::class, 'line_user_id', 'id')
            ->where('edu_line_authorizes.authorizes_type', '=', 1);
    }

    public function studentAuthorizes()
    {
        return $this->hasMany(LineAuthorize::class, 'line_user_id', 'id')
            ->where('edu_line_authorizes.authorizes_type', '=', 0);
    }
}