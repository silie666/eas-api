<?php

namespace App\Http\Requests\StudentApi\Course;

use App\Http\Requests\PageRequest;

class IndexRequest extends PageRequest
{
    public function pageRules(): array
    {
        return [
            'course_name' => 'nullable|string',
            'course_date' => 'nullable|date',
            'bill_fees'   => 'nullable|integer',
        ];
    }

    public function pageAttributes(): array
    {
        return [
            'course_name' => '课程名称',
            'course_date' => '上课日期',
            'bill_fees'   => '账单费用',
        ];
    }
}