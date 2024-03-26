<?php

namespace App\Services\Course;

use App\Events\Course\Bill\PayingEvent;
use App\Models\Card\Card;
use App\Models\Course\StudentCourseBill;
use App\Services\BaseService;
use App\Services\SqlBuildService;
use Package\Exceptions\Client\BadRequestException;

class StudentCourseBillService extends BaseService
{

    /**
     * 学生课程账单查询对象
     *
     * @param array $attributes
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function query(array $attributes = [])
    {
        $query = StudentCourseBill::query();

        $query = SqlBuildService::buildEqualQuery($query, $attributes, [
            'student_id' => 'student_id',
            'pay_status' => 'pay_status',
        ]);

        $query->orderByDesc('id');
        return $query;
    }

    /**
     * 获取学生课程账单
     *
     * @param int  $studentCourseBillId
     * @param bool $throw
     *
     * @return \Illuminate\Database\Eloquent\Builder|\App\Models\Course\StudentCourseBill
     */
    public static function getStudentCourseBill(int $studentCourseBillId, bool $throw = true)
    {
        $query = StudentCourseBill::query();
        if ($throw) {
            return $query->findOrFail($studentCourseBillId);
        }
        return $query->find($studentCourseBillId);
    }


    /**
     * 支付
     *
     * @param \App\Models\Card\Card                $card
     * @param \App\Models\Course\StudentCourseBill $studentCourseBill
     *
     * @return \App\Models\Course\StudentCourseBill
     */
    public static function pay(Card $card, StudentCourseBill $studentCourseBill)
    {
        if (!$studentCourseBill->fsmCan('pay_status', 'paying')) {
            throw new BadRequestException('账单无法支付！');
        }
        $studentCourseBill->fsmProcess('pay_status', 'paying', [
            'card_number' => $card->number,
        ]);

        event(new PayingEvent($studentCourseBill));
        return $studentCourseBill;
    }

    /**
     * 创建
     *
     * @param array $attributes
     * @param bool  $isProcessed
     *
     * @return \App\Models\Course\StudentCourseBill|\Illuminate\Database\Eloquent\Model
     */
    public static function create(array $attributes, bool $isProcessed = false)
    {
        if (!$isProcessed) {
            $attributes = self::processAttributes($attributes);
        }
        return StudentCourseBill::create($attributes);
    }

    /**
     * 处理属性
     *
     * @param array $attributes
     *
     * @return array
     */
    public static function processAttributes(
        array $attributes,
        StudentCourseBill $studentCourseBill = null
    ): array {
        $collect = collect($attributes);

        return $collect->all();
    }
}