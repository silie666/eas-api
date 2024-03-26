<?php

namespace App\Http\Controllers\StudentApi\Course;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentApi\Course\BillIndexRequest;
use App\Http\Requests\StudentApi\Course\BillPayRequest;
use App\Http\Requests\StudentApi\Course\IndexRequest;
use App\Http\Resources\StudentApi\Course\CourseBillResource;
use App\Http\Resources\StudentApi\Course\CourseResource;
use App\Http\Resources\StudentApi\EmptyResource;
use App\Services\Course\StudentCourseBillService;
use App\Services\Course\StudentCourseService;

class CourseController extends Controller
{
    /**
     * 课程-课程管理-课程列表
     *
     * @param \App\Http\Requests\StudentApi\Course\IndexRequest $request
     *
     * @return \App\Http\Resources\StudentApi\Course\CourseResource[]
     */
    public function index(IndexRequest $request)
    {
        $validated = $request->validated();
        \Arr::set($validated, 'student_id', \Auth::user()->id);
        $perPage = $request->getPerPage();

        $courses = StudentCourseService::query($validated)->with(['course'])->paginate($perPage);

        return CourseResource::collection($courses);
    }

    /**
     * 账单-账单管理-账单列表
     *
     * @param \App\Http\Requests\StudentApi\Course\BillIndexRequest $request
     *
     * @return \App\Http\Resources\StudentApi\Course\CourseBillResource[]
     */
    public function bills(BillIndexRequest $request)
    {
        $validated = $request->validated();
        \Arr::set($validated, 'student_id', \Auth::user()->id);
        $perPage = $request->getPerPage();
        $bills   = StudentCourseBillService::query($validated)->paginate($perPage);

        return CourseBillResource::collection($bills);
    }

    /**
     * 账单-账单管理-账单支付
     *
     * @param \App\Http\Requests\StudentApi\Course\BillPayRequest $request
     * @param int                                                 $courseBillId
     *
     * @return \App\Http\Resources\StudentApi\EmptyResource
     */
    public function pay(BillPayRequest $request, int $courseBillId)
    {
        ['number' => $number] = $request->validated();
        $courseBill = StudentCourseBillService::getStudentCourseBill($courseBillId);
        $card       = \Auth::user()->cards()->where('number', $number)->firstOrFail();
        $courseBill = StudentCourseBillService::pay($card, $courseBill);

        return new EmptyResource($courseBill);
    }
}