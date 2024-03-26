<?php

namespace Database\Factories\Course;


use App\Models\Course\Course;
use App\Models\Course\CourseBill;
use Database\Factories\BaseFactory;

class CourseBillFactory extends BaseFactory
{

    protected $model = CourseBill::class;

    public function definition()
    {
        return [
            'status'     => cons('common.is.no'),
            'teacher_id' => $this->firstTeacher()->id,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (CourseBill $course) {
        });
    }

    public function withCourse(Course $course)
    {
        return $this->state([
            'course_ids' => [$course->id],
        ]);
    }
}