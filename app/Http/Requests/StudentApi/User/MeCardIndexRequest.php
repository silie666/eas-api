<?php

namespace App\Http\Requests\StudentApi\User;

use App\Http\Requests\PageRequest;

class MeCardIndexRequest extends PageRequest
{
    public function pageRules(): array
    {
        return [
            'number'   => 'nullable|string',
            'with_all' => 'nullable|silie_bool',
        ];
    }

    public function pageAttributes(): array
    {
        return [
            'number'   => '卡号',
            'with_all' => '获取所有数据',
        ];
    }
}