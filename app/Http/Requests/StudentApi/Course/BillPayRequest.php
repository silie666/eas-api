<?php

namespace App\Http\Requests\StudentApi\Course;

use App\Http\Requests\StudentApi\Request;

class BillPayRequest extends Request
{
    public function rules(): array
    {
        return [
            'number' => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'number' => '卡号',
        ];
    }
}