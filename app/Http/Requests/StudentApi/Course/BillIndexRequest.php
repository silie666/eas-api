<?php

namespace App\Http\Requests\StudentApi\Course;

use App\Http\Requests\PageRequest;
use Illuminate\Validation\Rule;

class BillIndexRequest extends PageRequest
{
    public function pageRules(): array
    {
        return [
            'pay_status' => 'nullable|silie_int:tiny|' . Rule::in(cons('course.student.bill.pay_status')),
        ];
    }

    public function pageAttributes(): array
    {
        return [
            'pay_status' => '账单状态',
        ];
    }
}