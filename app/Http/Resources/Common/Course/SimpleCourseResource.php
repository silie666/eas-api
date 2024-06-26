<?php

namespace App\Http\Resources\Common\Course;

use App\Http\Resources\Common\BaseResource;

class SimpleCourseResource extends BaseResource
{
    public static function properties(): array
    {
        return [
            'name'        => static::propString('课程名称'),
            'date'        => static::propNullableDatetime('课程时间'),
            'content'     => static::propNullableString('课程内容'),
            'create_time' => static::propDatetime('创建时间'),
        ];
    }
}