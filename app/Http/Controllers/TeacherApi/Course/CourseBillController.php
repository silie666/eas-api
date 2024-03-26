<?php

namespace App\Http\Controllers\TeacherApi\Course;

use App\Http\Controllers\Controller;
use App\Http\Requests\TeacherApi\Course\CourseBillCreateOrUpdateRequest;
use App\Http\Requests\TeacherApi\Course\CourseBillIndexRequest;
use App\Http\Requests\TeacherApi\EmptyRequest;
use App\Http\Resources\TeacherApi\Course\CourseBillResource;
use App\Http\Resources\TeacherApi\EmptyResource;
use App\Services\Course\CourseBillService;

class CourseBillController extends Controller
{

    /**
     * 账单-账单管理-账单列表
     *
     * @param \App\Http\Requests\TeacherApi\Course\CourseBillIndexRequest $request
     *
     * @return \App\Http\Resources\TeacherApi\Course\CourseBillResource[]
     */
    public function index(CourseBillIndexRequest $request)
    {
        $validated   = $request->validated();
        $perPage     = $request->getPerPage();
        $courseBills = CourseBillService::query($validated)->paginate($perPage);
        return CourseBillResource::collection($courseBills);
    }

    /**
     * 账单-账单管理-账单详情
     *
     * @param \App\Http\Requests\TeacherApi\EmptyRequest $request
     * @param int                                        $courseBillId
     *
     * @return \App\Http\Resources\TeacherApi\Course\CourseBillResource
     */
    public function show(EmptyRequest $request, int $courseBillId)
    {
        $courseBill = CourseBillService::getCourseBill($courseBillId);
        return new CourseBillResource($courseBill);
    }


    /**
     * 账单-账单管理-账单添加
     *
     * @param \App\Http\Requests\TeacherApi\Course\CourseBillCreateOrUpdateRequest $request
     *
     * @return \App\Http\Resources\TeacherApi\Course\CourseBillResource
     */
    public function store(CourseBillCreateOrUpdateRequest $request)
    {
        $user       = \Auth::user();
        $courseBill = CourseBillService::create($request->validated(), $user);
        return new CourseBillResource($courseBill);
    }

    /**
     * 账单-账单管理-账单更新
     *
     * @param \App\Http\Requests\TeacherApi\Course\CourseBillCreateOrUpdateRequest $request
     * @param int                                                                  $courseBillId
     *
     * @return \App\Http\Resources\TeacherApi\Course\CourseBillResource
     */
    public function update(CourseBillCreateOrUpdateRequest $request, int $courseBillId)
    {
        $validated  = $request->validated();
        $user       = \Auth::user();
        $courseBill = CourseBillService::getCourseBill($courseBillId);
        $courseBill = CourseBillService::update($validated, $courseBill, $user);
        return new CourseBillResource($courseBill);
    }

    /**
     * 账单-账单管理-账单删除
     *
     * @param \App\Http\Requests\TeacherApi\EmptyRequest $request
     * @param int                                        $courseBillId
     *
     * @return \App\Http\Resources\TeacherApi\EmptyResource
     */
    public function destroy(EmptyRequest $request, int $courseBillId)
    {
        $courseBill = CourseBillService::getCourseBill($courseBillId);
        CourseBillService::delete($courseBill);
        return new EmptyResource();
    }

    /**
     * 账单-账单管理-发送账单
     *
     * @param \App\Http\Requests\TeacherApi\EmptyRequest $request
     * @param int                                        $courseBillId
     *
     * @return \App\Http\Resources\TeacherApi\EmptyResource
     */
    public function sendBill(EmptyRequest $request, int $courseBillId)
    {
        $courseBill = CourseBillService::getCourseBill($courseBillId);
        CourseBillService::sendBill($courseBill);
        return new EmptyResource();
    }
}