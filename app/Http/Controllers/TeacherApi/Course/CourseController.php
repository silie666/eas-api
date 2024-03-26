<?php

namespace App\Http\Controllers\TeacherApi\Course;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeacherApi\Course\CreateOrUpdateRequest;
use App\Http\Requests\TeacherApi\Course\IndexRequest;
use App\Http\Requests\TeacherApi\EmptyRequest;
use App\Http\Resources\TeacherApi\Course\CourseResource;
use App\Http\Resources\TeacherApi\EmptyResource;
use App\Services\Course\CourseService;

class CourseController extends Controller
{
    /**
     * 课程-课程管理-课程列表
     *
     * @param \App\Http\Requests\TeacherApi\Course\IndexRequest $request
     *
     * @return \App\Http\Resources\TeacherApi\Course\CourseResource[]
     */
    public function index(IndexRequest $request)
    {
        $validated = $request->validated();
        $perPage   = $request->getPerPage();
        $courses   = CourseService::query($validated)->paginate($perPage);
        return CourseResource::collection($courses);
    }

    /**
     * 课程-课程管理-课程详情
     *
     * @param \App\Http\Requests\TeacherApi\EmptyRequest $request
     * @param int                                        $courseId
     *
     * @return \App\Http\Resources\TeacherApi\Course\CourseResource
     */
    public function show(EmptyRequest $request, int $courseId)
    {
        $course = CourseService::getCourse($courseId);
        return new CourseResource($course);
    }

    /**
     * 课程-课程管理-课程添加
     *
     * @param \App\Http\Requests\TeacherApi\Course\CreateOrUpdateRequest $request
     *
     * @return \App\Http\Resources\TeacherApi\Course\CourseResource
     */
    public function store(CreateOrUpdateRequest $request)
    {
        $user   = \Auth::user();
        $course = CourseService::create($request->validated(), $user);
        return new CourseResource($course);
    }

    /**
     * 课程-课程管理-课程更新
     *
     * @param \App\Http\Requests\TeacherApi\Course\CreateOrUpdateRequest $request
     * @param int                                                        $courseId
     *
     * @return \App\Http\Resources\TeacherApi\Course\CourseResource
     */
    public function update(CreateOrUpdateRequest $request, int $courseId)
    {
        $validated = $request->validated();
        $user      = \Auth::user();
        $course    = CourseService::getCourse($courseId);
        $course    = CourseService::update($validated, $course, $user);
        return new CourseResource($course);
    }

    /**
     * 课程-课程管理-课程删除
     *
     * @param \App\Http\Requests\TeacherApi\EmptyRequest $request
     * @param int                                        $courseId
     *
     * @return \App\Http\Resources\TeacherApi\EmptyResource
     */
    public function destroy(EmptyRequest $request, int $courseId)
    {
        $course = CourseService::getCourse($courseId);
        CourseService::delete($course);
        return new EmptyResource();
    }

}