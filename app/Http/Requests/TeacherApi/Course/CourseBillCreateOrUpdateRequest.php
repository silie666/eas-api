<?php

namespace App\Http\Requests\TeacherApi\Course;

use App\Http\Requests\TeacherApi\Request;

class CourseBillCreateOrUpdateRequest extends Request
{
    public function rules()
    {
        return [
            'course_ids'   => 'required|array',
            'course_ids.*' => 'required|silie_int',
        ];
    }

    public function attributes()
    {
        return [
            'course_ids'   => '课程IDs',
            'course_ids.*' => '课程ID',
        ];
    }
}