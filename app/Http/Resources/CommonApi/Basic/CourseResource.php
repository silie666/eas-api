<?php

namespace App\Http\Resources\CommonApi\Basic;

use App\Http\Resources\CommonApi\BaseResource;

class CourseResource extends BaseResource
{
    public static function properties(): array
    {
        return [
            'id'   => static::propInt('ID'),
            'name' => static::propString('课程名称'),
        ];
    }
}