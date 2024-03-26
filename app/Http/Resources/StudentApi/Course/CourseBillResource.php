<?php

namespace App\Http\Resources\StudentApi\Course;

use App\Http\Resources\StudentApi\BaseResource;

class CourseBillResource extends BaseResource
{
    public static function properties(): array
    {
        return [
            'id'              => static::propInt('ID'),
            'pay_status'      => static::propInt('支付状态'),
            'pay_status_name' => static::propString('支付状态名称', mutator: function () {
                return cons()->valueLang('course.student.bill.pay_status', $this->pay_status);
            }),
            'bill_fees'       => static::propBigInt('账单费用'),
            'pay_time'        => static::propNullableDatetime('支付时间'),
            'paid_fees'       => static::propBigInt('支付金额'),
            'course'          => static::propWhenLoadedModel('课程', SimpleCourseResource::class),
        ];
    }
}