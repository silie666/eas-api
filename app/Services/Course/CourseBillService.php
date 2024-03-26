<?php

namespace App\Services\Course;

use App\Models\Course\CourseBill;
use App\Models\User\Teacher;
use App\Services\BaseService;
use App\Services\SqlBuildService;
use Package\Exceptions\Client\BadRequestException;

class CourseBillService extends BaseService
{

    /**
     * 课程查询对象
     *
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function query(array $attributes = [])
    {
        $query = CourseBill::query();
        $query = SqlBuildService::buildEqualQuery($query, $attributes, [
            'status' => 'status',
        ]);

        $query->orderByDesc('id');
        return $query;
    }

    /**
     * 获取课程账单
     *
     * @param int  $courseBillId
     * @param bool $throw
     *
     * @return \Illuminate\Database\Eloquent\Builder|\App\Models\Course\CourseBill
     */
    public static function getCourseBill(int $courseBillId, bool $throw = true)
    {
        $query = CourseBill::query();
        if ($throw) {
            return $query->findOrFail($courseBillId);
        }
        return $query->find($courseBillId);
    }

    /**
     * 创建
     *
     * @param array                    $attributes
     * @param \App\Models\User\Teacher $teacher
     * @param bool                     $isProcessed
     *
     * @return \App\Models\Course\CourseBill
     */
    public static function create(array $attributes, Teacher $teacher, bool $isProcessed = false)
    {
        if (!$isProcessed) {
            $attributes = static::processAttributes($attributes);
        }

        $courseBill = CourseBill::create(array_merge($attributes, [
            'teacher_id' => $teacher->id,
            'status'     => false,
        ]));

        return $courseBill;
    }

    /**
     * 更新
     *
     * @param array                         $attributes
     * @param \App\Models\Course\CourseBill $courseBill
     * @param \App\Models\User\Teacher      $teacher
     * @param bool                          $isProcessed
     *
     * @return \App\Models\Course\CourseBill
     */
    public static function update(
        array $attributes,
        CourseBill $courseBill,
        Teacher $teacher,
        bool $isProcessed = false
    ) {
        if (!$isProcessed) {
            $attributes = static::processAttributes($attributes, $courseBill);
        }
        $courseBill->update($attributes);

        return $courseBill;
    }

    /**
     * 删除
     *
     * @param \App\Models\Course\CourseBill $courseBill
     *
     * @return void
     */
    public static function delete(CourseBill $courseBill)
    {
        if ($courseBill->status === cons('common.is.yes')) {
            throw new BadRequestException('已发送账单，无法删除');
        }
        $courseBill->delete();
    }

    /**
     * 处理属性
     *
     * @param array                              $attributes
     * @param \App\Models\Course\CourseBill|null $courseBill
     *
     * @return array
     */
    public static function processAttributes(array $attributes = [], CourseBill $courseBill = null)
    {
        $collect = collect($attributes);

        if ($courseBill) {
            if ($courseBill->status === cons('common.is.yes')) {
                throw new BadRequestException('已发送账单，无法删除');
            }
        }

        return $collect->all();
    }

    /**
     * 发送账单
     *
     * @param \App\Models\Course\CourseBill $courseBill
     *
     * @return \App\Models\Course\CourseBill
     */
    public static function sendBill(CourseBill $courseBill)
    {
        if ($courseBill->status === cons('common.is.yes')) {
            throw new BadRequestException('课程账单已发送！');
        }
        foreach ($courseBill->courses as $course) {
            foreach ($course->student_ids as $studentId) {
                StudentCourseBillService::create(
                    [
                        'course_id'      => $course->id,
                        'course_bill_id' => $courseBill->id,
                        'teacher_id'     => $courseBill->teacher_id,
                        'student_id'     => $studentId,
                        'bill_fees'      => $course->fees,
                        'pay_status'     => cons('course.student.bill.pay_status.unpaid'),
                    ]
                );
            }
        }
        $courseBill->update([
            'status' => cons('common.is.yes'),
        ]);
        return $courseBill;
    }
}