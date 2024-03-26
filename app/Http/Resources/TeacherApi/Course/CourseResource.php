<?php

namespace App\Http\Resources\TeacherApi\Course;

use App\Http\Resources\Common\User\UserResource;
use App\Http\Resources\TeacherApi\BaseResource;

class CourseResource extends BaseResource
{
    public static function properties(): array
    {
        return [
            'id'          => static::propInt('ID'),
            'name'        => static::propString('课程名称'),
            'date'        => static::propDatetime('课程时间'),
            'fees'        => static::propBigInt('课程费用'),
            'content'     => static::propNullableString('课程内容'),
            'student_ids' => static::propArray('学生ID', format: 'number'),

            'students' => static::propCollection('学生', UserResource::class),
            'create_time' => static::propDatetime('创建时间'),
        ];
    }
}