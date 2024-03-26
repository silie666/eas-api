<?php

namespace App\Events\Course\Bill;


use App\Events\BaseEvent;
use App\Models\Course\StudentCourseBill;

class PayingEvent extends BaseEvent
{
    public StudentCourseBill $studentCourseBill;

    public function __construct(StudentCourseBill $studentCourseBill)
    {
        parent::__construct();
        $this->studentCourseBill = $studentCourseBill;
    }
}