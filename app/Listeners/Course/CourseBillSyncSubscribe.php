<?php

namespace App\Listeners\Course;


use App\Events\Course\Bill\PaidEvent;
use App\Events\Course\Bill\PayingEvent;
use App\Services\Course\StudentCourseService;
use Carbon\Carbon;

class CourseBillSyncSubscribe extends ShouldQueue
{
    public function onPaying(PayingEvent $event)
    {
        $studentCourseBill = $event->studentCourseBill;

        // todo 支付请求
        try {
            $token = \OmiseToken::create([
                'card' => [
                    'name'             => $studentCourseBill->card->brand_name,
                    'number'           => $studentCourseBill->card->number,
                    'expiration_month' => $studentCourseBill->card->expiration_date->month,
                    'expiration_year'  => $studentCourseBill->card->expiration_date->year,
                ],
            ])->offsetGet('id');
            // todo 目前只开通了一种货币，应该根据学生信用卡确定货币类型
            $order = \OmiseCharge::create([
                'amount'      => $studentCourseBill->bill_fees,
                'currency'    => cons()->key('omise.currency', cons('omise.currency.jpy')),
                'description' => 'buy ' . $studentCourseBill->course->name,
                'card'        => $token,
            ]);
        } catch (\Throwable $e) {
            $studentCourseBill->fsmProcess('pay_status', 'fail');
            \Log::error('omise pay error', [
                'error' => $e->getMessage(),
            ]);
            return;
        }

        $order = collect($order->toArray());
        if ($order->get('status') === 'successful') {
            $studentCourseBill->fsmProcess('pay_status', 'paid', [
                'paid_fees' => $studentCourseBill->bill_fees,
                'pay_time'  => Carbon::now(),
            ]);
            event(new PaidEvent($studentCourseBill));
        } else {
            $studentCourseBill->fsmProcess('pay_status', 'fail');
        }

        $studentCourseBill->update([
            'extra_data' => [
                'omise_order' => $order,
            ],
        ]);


    }

    public function onPaid(PaidEvent $event)
    {
        $studentCourseBill = $event->studentCourseBill;
        // 支付成功，发放课程
        StudentCourseService::create([
            'course_id'              => $studentCourseBill->course_id,
            'student_id'             => $studentCourseBill->student_id,
            'student_course_bill_id' => $studentCourseBill->id,
        ]);
    }
}