<?php

namespace App\Models\Course;

use App\Models\Card\Card;
use App\Models\FSMStatus;
use App\Models\Model;
use App\Models\Traits\FSMTrait;
use App\Models\User\Student;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentCourseBill extends Model
{
    use HasFactory, FSMTrait;

    protected $fillable = [
        'course_bill_id',
        'student_id',
        'course_id',
        'card_number',
        'pay_status',
        'bill_fees',
        'paid_fees',
        'extra_data',
        'extra_data->omise_order',
    ];

    protected $casts = [
        'pay_time'   => 'datetime',
        'extra_data' => 'array',
    ];

    /**
     * FSM 常量列表
     *
     * @return string[]
     */
    protected function fsmConstants(): array
    {
        return [
            'pay_status' => 'course.student.bill.pay_status',
        ];
    }

    /**
     * FSM 事务列表
     *
     * @return array
     */
    protected function fsmTransitions(): array
    {
        return [
            'pay_status' => FSMStatus::courceBillPayStatusTransitions(),
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function card()
    {
        return $this->belongsTo(Card::class, 'card_number', 'number');
    }

    public function courseBill()
    {
        return $this->belongsTo(CourseBill::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function studentCourses()
    {
        return $this->hasMany(StudentCourse::class, 'bill_id');
    }

    public function omiseOrder()
    {
        return Attribute::get(fn() => \Arr::get($this->extra_data, 'omise_order', []));
    }

}