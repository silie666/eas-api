<?php

namespace App\Http\Resources\StudentApi\Course;

use App\Http\Resources\StudentApi\BaseResource;

class CourseResource extends BaseResource
{
    public static function properties(): array
    {
        return [
            'id'     => static::propInt('ID'),
            'course' => static::propWhenLoadedModel('课程', SimpleCourseResource::class),
        ];
    }
}