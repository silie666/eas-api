<?php

namespace App\Http\Requests\TeacherApi\Course;

use App\Http\Requests\TeacherApi\Request;

class CreateOrUpdateRequest extends Request
{
    public function rules()
    {
        return [
            'name'          => 'required|string',
            'content'       => 'required|string',
            'date'          => 'required|' . $this->dateTimeRule,
            'fees'          => 'required|numeric|min:100',
            'student_ids'   => 'required|array',
            'student_ids.*' => 'required|silie_int',
        ];
    }

    public function attributes()
    {
        return [
            'name'          => '课程名称',
            'content'       => '课程内容',
            'date'          => '上课日期',
            'fees'          => '课程费用',
            'student_ids'   => '学生IDs',
            'student_ids.*' => '学生ID',
        ];
    }
}