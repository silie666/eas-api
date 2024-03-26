<?php

namespace App\Http\Resources\CommonApi\Basic;

use App\Http\Resources\CommonApi\BaseResource;

class StudentResource extends BaseResource
{
    public static function properties(): array
    {
        return [
            'id'   => static::propInt('ID'),
            'name' => static::propString('姓名'),
        ];
    }
}