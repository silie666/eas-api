<?php

namespace Database\Factories\Course;


use App\Models\Course\Course;
use App\Models\Course\StudentCourse;
use App\Models\Course\StudentCourseBill;
use App\Models\User\Student;
use Database\Factories\BaseFactory;

class StudentCourseFactory extends BaseFactory
{

    protected $model = StudentCourse::class;

    public function definition()
    {
    }

    public function configure()
    {
        return $this->afterCreating(function (StudentCourse $studentCourse) {

        });
    }

    public function withStudentCourseBill(StudentCourseBill $studentCourseBill)
    {
        return $this->state([
            'student_course_bill_id' => $studentCourseBill->id,
        ]);
    }

    public function withCourse(Course $course)
    {
        return $this->state([
            'course_id' => $course->id,
        ]);
    }

    public function withStudent(Student $student)
    {
        return $this->state([
            'student_id' => $student->id,
        ]);
    }
}