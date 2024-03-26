<?php

namespace Database\Factories\Course;


use App\Models\Course\Course;
use Database\Factories\BaseFactory;

class CourseFactory extends BaseFactory
{

    protected $model = Course::class;

    public function definition()
    {
        $faker = $this->faker;
        return [
            'teacher_id'  => $this->firstTeacher()->id,
            'name'        => $faker->name,
            'date'        => $faker->date,
            'fees'        => $faker->randomNumber(4),
            'content'     => $faker->text(),
            'student_ids' => [$this->firstStudent()->id],
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Course $course) {
        });
    }
}