<?php

namespace App\Http\Resources\Common\Course;

use App\Http\Resources\Common\BaseResource;

class StudentCourseResource extends BaseResource
{
    public static function properties(): array
    {
        return [
            'id'     => static::propInt('ID'),
            'course' => static::propWhenLoadedModel('课程', SimpleCourseResource::class),
        ];
    }
}