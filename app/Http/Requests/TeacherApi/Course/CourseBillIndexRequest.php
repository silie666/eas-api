<?php

namespace App\Http\Requests\TeacherApi\Course;

use App\Http\Requests\PageRequest;
use Illuminate\Validation\Rule;

class CourseBillIndexRequest extends PageRequest
{
    public function pageRules(): array
    {
        return [
            'status' => 'nullable|silie_bool|' . Rule::in(cons('common.is')),
        ];
    }

    public function pageAttributes(): array
    {
        return [
            'status' => '状态',
        ];
    }
}