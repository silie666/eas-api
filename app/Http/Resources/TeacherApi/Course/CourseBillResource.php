<?php

namespace App\Http\Resources\TeacherApi\Course;

use App\Http\Resources\Common\Course\StudentCourseBillResource;
use App\Http\Resources\TeacherApi\BaseResource;

class CourseBillResource extends BaseResource
{
    public static function properties(): array
    {
        return [
            'id'          => static::propInt('ID'),
            'status'      => static::propBool('状态'),
            'status_name' => static::propString('状态名称', mutator: function () {
                return cons()->valueLang('common.is', $this->status);
            }),
            'course_ids'  => static::propArray('课程IDs', format: 'number'),
            'courses'     => static::propCollection('课程', SimpleCourseResource::class),
            'create_time' => static::propDatetime('创建时间'),

            'student_course_bills' => static::propWhenLoadedModels('学生账单', StudentCourseBillResource::class),
        ];
    }
}