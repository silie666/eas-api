<?php

namespace App\Http\Resources\TeacherApi\Course;

use App\Http\Resources\TeacherApi\BaseResource;

class SimpleCourseResource extends BaseResource
{
    public static function properties(): array
    {
        return [
            'id'   => static::propInt('ID'),
            'name' => static::propString('课程名称'),
        ];
    }
}