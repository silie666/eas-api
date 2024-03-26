<?php

namespace App\Http\Requests\TeacherApi\Course;

use App\Http\Requests\PageRequest;

class IndexRequest extends PageRequest
{
    public function pageRules(): array
    {
        return [
            'name' => 'nullable|string',
            'date' => 'nullable|date',
            'fees' => 'nullable|numeric',
        ];
    }

    public function pageAttributes(): array
    {
        return [
            'name' => '课程名称',
            'date' => '上课日期',
            'fees' => '课程费用',
        ];
    }
}