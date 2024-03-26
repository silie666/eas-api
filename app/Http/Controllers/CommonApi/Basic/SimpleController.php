<?php

namespace App\Http\Controllers\CommonApi\Basic;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommonApi\EmptyRequest;
use App\Http\Resources\CommonApi\Basic\CourseResource;
use App\Http\Resources\CommonApi\Basic\StudentResource;
use App\Models\Course\Course;
use App\Models\User\Student;

class SimpleController extends Controller
{
    /**
     * 学生列表
     *
     * @param \App\Http\Requests\CommonApi\EmptyRequest $request
     *
     * @return \App\Http\Resources\CommonApi\Basic\StudentResource[]
     */
    public function student(EmptyRequest $request)
    {
        $devices = Student::query()
            ->select(['id', 'name'])
            ->get();
        return StudentResource::collection($devices);
    }

    /**
     * 课程列表
     *
     * @param \App\Http\Requests\CommonApi\EmptyRequest $request
     *
     * @return \App\Http\Resources\CommonApi\Basic\CourseResource[]
     */
    public function courses(EmptyRequest $request)
    {
        $devices = Course::query()
            ->select(['id', 'name'])
            ->get();
        return CourseResource::collection($devices);
    }
}