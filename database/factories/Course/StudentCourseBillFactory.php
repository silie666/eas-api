<?php

namespace Database\Factories\Course;


use App\Models\Course\Course;
use App\Models\Course\CourseBill;
use App\Models\Course\StudentCourseBill;
use App\Models\User\Student;
use Database\Factories\BaseFactory;

class StudentCourseBillFactory extends BaseFactory
{

    protected $model = StudentCourseBill::class;

    public function definition()
    {
        return [
            'pay_status'  => cons('course.student.bill.pay_status.unpaid'),
            'card_number' => $this->faker->creditCardNumber,
            'student_id'  => $this->firstStudent()->id,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (StudentCourseBill $studentCourseBill) {
            $this->fillCourseBill($studentCourseBill);
            $studentCourseBill->save();
        });
    }

    protected function fillCourseBill(StudentCourseBill $studentCourseBill)
    {
        if ($studentCourseBill->courseBill) {
            return;
        }
        $course     = Course::factory()->create();
        $courseBill = CourseBill::factory()->withCourse($course)->create();
        $studentCourseBill->fill([
            'course_bill_id' => $courseBill->id,
            'course_id'      => $course->id,
            'bill_fees'      => $course->fees,
        ]);
    }

    public function withCourseBill(CourseBill $courseBill)
    {
        return $this->state([
            'course_bill_id' => $courseBill->id,
        ]);
    }

    public function withStudent(Student $student)
    {
        return $this->state([
            'student_id' => $student->id,
        ]);
    }

    public function withCardNumber($cardNumber)
    {
        return $this->state([
            'card_number' => $cardNumber,
        ]);
    }

    public function withCourse(Course $course)
    {
        return $this->state([
            'course_id' => $course->id,
            'bill_fees' => $course->fees,
        ]);
    }
}